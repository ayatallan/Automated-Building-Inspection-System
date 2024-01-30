#!/usr/bin/env python

import rospy
from sensor_msgs.msg import Image
from move_base_msgs.msg import MoveBaseAction, MoveBaseGoal
import actionlib
from actionlib_msgs.msg import GoalStatus
from cv_bridge import CvBridge
import cv2
import numpy as np
from threading import Thread
from tensorflow import keras
from keras.preprocessing import image
from geometry_msgs.msg import PoseStamped
from nav_msgs.msg import Odometry
from io import StringIO
from geometry_msgs.msg import Twist
from actionlib_msgs.msg import *

class ImageClassifierAndNavigator():
    def __init__(self):
        rospy.init_node('image_classifier_and_navigator', anonymous=False)
       

        self.model = keras.models.load_model('/home/raghad/turtlebot2_ws/src/my_kinect_stream/keras_model.h5')

        # Compile the model (you can adjust the optimizer, loss, and metrics based on your original compilation)
        self.model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])

        # Load the class labels from the desktop
        with open('/home/raghad/turtlebot2_ws/src/my_kinect_stream/labels.txt', 'r') as file:
            self.class_labels = [line.strip() for line in file]
        # Initialize the CvBridge
        self.bridge = CvBridge()
        self.last_image_time = rospy.Time.now()

    # Minimum time interval between processing images (in seconds)
        self.min_interval = rospy.Duration(0.1) 
        # Image classification setup
        # self.setup_image_classification()
       # ROS Subscriber for the Kinect RGB image
        rospy.Subscriber('/camera/rgb/image_raw', Image, self.image_callback)
        # Navigation setup
       # Navigation setup
        
        self.pub = rospy.Publisher('/cmd_vel_mux/input/navi', Twist, queue_size=1)


        self.setup_navigation()
    

        # Throttle the image callback to a maximum frequency
        self.rate = rospy.Rate(2)  # Adjust the frequency as needed
 
        rospy.spin()

  

    
    def setup_navigation(self):
        # Navigation setup
        self.move_base = actionlib.SimpleActionClient("move_base", MoveBaseAction)
        rospy.loginfo("Wait for the action server to come up")
        # Allow up to 5 seconds for the action server to come up
        self.move_base.wait_for_server(rospy.Duration(5))
        # Print a message before initiating navigation
        rospy.loginfo("Initiating navigation")


        twist = Twist()
        twist.linear.x = 0.1  # Decreased linear velocity
        twist.angular.z = 0.0
        
        self.pub.publish(twist)

        # Navigation goal setup
        first_goal = MoveBaseGoal()
        first_goal.target_pose.header.frame_id = 'base_link'
        first_goal.target_pose.header.stamp = rospy.Time.now()
        first_goal.target_pose.pose.position.x = 2.8
        first_goal.target_pose.pose.orientation.w = 1
        self.move_base.send_goal(first_goal)
        

        success = self.move_base.wait_for_result(rospy.Duration(60))
        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move forward 2 meters for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved 2 meters forward")


        # # Define and send the second goal to move left by 1 meter
        second_goal = MoveBaseGoal()
        second_goal.target_pose.header.frame_id = 'base_link'
        second_goal.target_pose.header.stamp = rospy.Time.now()
        second_goal.target_pose.pose.position.y = 2.2
        second_goal.target_pose.pose.orientation.w = 0.5
        self.move_base.send_goal(second_goal)
        success = self.move_base.wait_for_result(rospy.Duration(60))

        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move left 2 meter for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved left 2 meter")

        # Define and send the third goal to move backward by 1 meter
        third_goal = MoveBaseGoal()
        third_goal.target_pose.header.frame_id = 'base_link'
        third_goal.target_pose.header.stamp = rospy.Time.now()
        third_goal.target_pose.pose.position.x = -2
        third_goal.target_pose.pose.orientation.w = 1.0
        self.move_base.send_goal(third_goal)
        success = self.move_base.wait_for_result(rospy.Duration(60))

        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move backward 2 meter for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved backward 2 meter")


  # # Define and send the second goal to move left by 1 meter
        fourth_goal = MoveBaseGoal()
        fourth_goal.target_pose.header.frame_id = 'base_link'
        fourth_goal.target_pose.header.stamp = rospy.Time.now()
        fourth_goal.target_pose.pose.position.y = -2
        fourth_goal.target_pose.pose.orientation.w = 0.5
        self.move_base.send_goal(fourth_goal)
        success = self.move_base.wait_for_result(rospy.Duration(60))

        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move left 1 meter for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved left 1 meter")




    def image_callback(self, msg):
        try:
            # Throttle the image processing
            if rospy.Time.now() - self.last_image_time < self.min_interval:
                return

            self.last_image_time = rospy.Time.now()

        

            roi_x = 150  # X-coordinate of the top-left corner of the ROI
            roi_y = 150  # Y-coordinate of the top-left corner of the ROI
            roi_width = 224  # Width of the ROI
            roi_height = 224  # Height of the ROI

            # Convert the ROS Image message to an OpenCV image
            frame = self.bridge.imgmsg_to_cv2(msg, desired_encoding="bgr8")
            roi = frame[roi_y:roi_y + roi_height, roi_x:roi_x + roi_width]

        # Preprocess the ROI for prediction
            roi = cv2.resize(roi, (224, 224))
            img_array = image.img_to_array(roi)
            img_array = np.expand_dims(img_array, axis=0)
            img_array = img_array / 255.0  # Normalize the image data
            # Process the frame in a separate thread for faster predictions
            predictions = self.model.predict(img_array)
            predicted_class_index = np.argmax(predictions[0])
            predicted_class = self.class_labels[predicted_class_index]

        # Print the predicted class
            self.check_class(predicted_class)
        # Display the frame with the predicted class name
            cv2.putText(frame, f" {predicted_class}", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
        # Show the processed image (optional)
            cv2.imshow("Processed Image", frame)
            cv2.waitKey(1)
            # Throttle the callback to control the frequency
            # self.rate.sleep()

        except Exception as e:
            rospy.logerr(f"Error processing image: {e}")


    def check_class(self, predicted_class):
        if  "cracks" in predicted_class.lower() or "structural" in predicted_class.lower():
            # rospy.sleep(5.0)
            print(f"Predicted class: {predicted_class}") 
            self.print_robot_position()
        else:
            print(f"Predicted class: {predicted_class}")

    def print_robot_position(self):
        # Get the robot's current position from the /odom topic
        odom_data = rospy.wait_for_message("/odom", Odometry)
        x = odom_data.pose.pose.position.x
        y = odom_data.pose.pose.position.y
        rospy.loginfo(f"Current robot position: x = {x}, y = {y}")

    def shutdown(self):
        rospy.loginfo("Stopping image_classifier_and_navigator node")

if __name__ == '__main__':
    try:
        node = ImageClassifierAndNavigator()
        rospy.on_shutdown(node.shutdown)
    except rospy.ROSInterruptException:
        rospy.loginfo("Exception thrown")
