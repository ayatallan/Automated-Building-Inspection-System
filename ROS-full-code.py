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
from geometry_msgs.msg import Twist
import paho.mqtt.client as paho
import random
import time
from paho import mqtt

class ImageClassifierAndNavigator():
    def __init__(self):
        rospy.init_node('image_classifier_and_navigator', anonymous=False)
        self.last_x = 0.0
        self.last_y = 0.0
        self.last_orientation = None
        # Load the model
        self.model = keras.models.load_model('/home/raghad/turtlebot2_ws/src/my_kinect_stream/keras_model.h5')
        self.model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])

        # Load class labels
        with open('/home/raghad/turtlebot2_ws/src/my_kinect_stream/labels.txt', 'r') as file:
            self.class_labels = [line.strip() for line in file]

        self.bridge = CvBridge()

        # MQTT client setup
        self.client = paho.Client()
        client_name_id = "set_here_client_id_" + str(random.randint(1, 100)) + "_" + str(random.randint(1, 100))
        self.client = paho.Client(client_id=client_name_id, userdata=None, protocol=paho.MQTTv31)
        self.client.on_connect = self.on_connect
        self.client.connect("mqtt-dashboard.com", 1883)
        self.client.on_subscribe = self.on_subscribe
        self.client.on_message = self.on_message
        self.client.on_publish = self.on_publish
        self.client.subscribe("any_topic_here_to_subscribe/in/our/project", qos=1)
        self.client.loop_start()  # Start the MQTT client loop

        self.last_image_time = rospy.Time.now()

        # Minimum time interval between processing images (in seconds)
        self.min_interval = rospy.Duration(0.1) 
        # rospy.Subscriber('/camera/depth/image_raw', Image)
        rospy.Subscriber('/camera/rgb/image_raw', Image, self.image_callback)
        self.pub = rospy.Publisher('/cmd_vel_mux/input/navi', Twist, queue_size=1)
        self.setup_navigation()
        self.rate = rospy.Rate(2)  
        rospy.spin()

    def depth(self):
        try:
            depth_image = None
            bridge = CvBridge()
            
            # Subscribe to the depth image topic
            depth_msg = rospy.wait_for_message('/camera/depth/image_raw', Image)
            
            # Convert ROS image message to OpenCV image
            depth_image = bridge.imgmsg_to_cv2(depth_msg, desired_encoding="passthrough")
            
            if depth_image is not None:
                # Get image dimensions
                height, width = depth_image.shape[:2]

                # Get depth value at center of the image
                center_x = width // 2
                center_y = height // 2
                depth_at_center = depth_image[center_y, center_x]

                # Convert depth value from millimeters to meters
                min_distance_to_object = depth_at_center / 1000.0
                
                rospy.loginfo(f"distance: {min_distance_to_object}")
                return min_distance_to_object
            
        except Exception as e:
            rospy.logerr(f"Error processing depth image: {e}")
            return None


    def setup_navigation(self):
        # Navigation setup
        self.move_base = actionlib.SimpleActionClient("move_base", MoveBaseAction)
        rospy.loginfo("Wait for the action server to come up")
        self.move_base.wait_for_server(rospy.Duration(5))
        rospy.loginfo("Initiating navigation")

        # Navigation goals setup
        first_goal = MoveBaseGoal()
        first_goal.target_pose.header.frame_id = 'base_link'
        first_goal.target_pose.header.stamp = rospy.Time.now()
        first_goal.target_pose.pose.position.x = 3
        first_goal.target_pose.pose.orientation.w =  1
        self.move_base.send_goal(first_goal)
        self.check_goal_completion()

        second_goal = MoveBaseGoal()
        second_goal.target_pose.header.frame_id = 'base_link'
        second_goal.target_pose.header.stamp = rospy.Time.now()
        second_goal.target_pose.pose.position.y = 3
        second_goal.target_pose.pose.orientation.w = 0.5
        self.move_base.send_goal(second_goal)
        self.check_goal_completion()

        third_goal = MoveBaseGoal()
        third_goal.target_pose.header.frame_id = 'base_link'
        third_goal.target_pose.header.stamp = rospy.Time.now()
        third_goal.target_pose.pose.position.x = -2.5
        third_goal.target_pose.pose.orientation.w = 1.0
        self.move_base.send_goal(third_goal)
        self.check_goal_completion()

        fourth_goal = MoveBaseGoal()
        fourth_goal.target_pose.header.frame_id = 'base_link'
        fourth_goal.target_pose.header.stamp = rospy.Time.now()
        fourth_goal.target_pose.pose.position.y = -2.7
        fourth_goal.target_pose.pose.orientation.w = 0.5
        self.move_base.send_goal(fourth_goal)
        self.check_goal_completion()

    def check_goal_completion(self):
        success = self.move_base.wait_for_result(rospy.Duration(60))
        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to reach the goal")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base reached the goal")

    def image_callback(self, msg):
        try:
            if rospy.Time.now() - self.last_image_time < self.min_interval:
                return
            self.last_image_time = rospy.Time.now()

            roi_x = 150
            roi_y = 150
            roi_width = 224
            roi_height = 224

            frame = self.bridge.imgmsg_to_cv2(msg, desired_encoding="bgr8")
            roi = frame[roi_y:roi_y + roi_height, roi_x:roi_x + roi_width]
          

            roi = cv2.resize(roi, (224, 224))
            img_array = image.img_to_array(roi)
            img_array = np.expand_dims(img_array, axis=0)
            img_array = img_array / 255.0

            predictions = self.model.predict(img_array)
            predicted_class_index = np.argmax(predictions[0])
            predicted_class = self.class_labels[predicted_class_index]
            
            # self.print_robot_position(predicted_class, min_distance_to_object)
            if "cracks" in predicted_class.lower() or "structural" in predicted_class.lower():
            #    rospy.sleep(5)
               min_distance_to_object = self.depth()
            #    if min_distance_to_object is not None:
               self.print_robot_position(predicted_class, min_distance_to_object)
            #    else:
                    # rospy.logwarn("Could not determine depth information.")

            cv2.putText(frame, f" {predicted_class}", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
            cv2.imshow("Processed Image", frame)
            cv2.waitKey(1)

        except Exception as e:
            rospy.logerr(f"Error processing image: {e}")


    def print_robot_position(self, predicted_class, min_distance_to_object):
        odom_data = rospy.wait_for_message("/odom", Odometry)
        # Extract current position and orientation
        current_x = odom_data.pose.pose.position.x
        current_y = odom_data.pose.pose.position.y
        current_orientation = odom_data.pose.pose.orientation
        # self.last_x = 0.0
        # self.last_y = 0.0
        # self.last_orientation = None

        # Calculate change in position since last call
        delta_x = current_x - self.last_x
        delta_y = current_y - self.last_y

        # Update last position
        self.last_x = current_x
        self.last_y = current_y

        # Determine the direction based on the change in position
        direction = None
        if abs(delta_x) > abs(delta_y):
            direction = "X"
            current_x += min_distance_to_object if delta_x > 0 else - min_distance_to_object
            # if delta_x > 0 else - min_distance_to_object
        elif abs(delta_y) > abs(delta_x):
            direction = "Y"
            current_y += min_distance_to_object if delta_y > 0 else - min_distance_to_object
        else:
            direction = "Unknown"

        # Log the robot position and direction
    
        rospy.loginfo(f"{predicted_class}, Robot position: x = {current_x}, y = {current_y}, Moving in direction: {direction}")
        # rospy.loginfo(f"{predicted_class}, Robot position: x = {current_x}, y = {current_y}, Moving in direction: {direction}")
        # Update last orientation
        rospy.loginfo (min_distance_to_object)
        self.last_orientation = current_orientation
        msg = "{:.1f},{:.1f},{}".format(current_x, current_y, predicted_class)
        self.publish_mqtt("project_topic/automated_building_inspection_system/location", str(msg))


    def on_connect(self, client, userdata, flags, rc, properties=None):
        print("CONNACK received with code %s." % rc)
        if rc == 0:
            print("Connected to MQTT Broker!")
        else:
            print("Failed to connect, return code %d\n", rc)

    def on_publish(self, client, userdata, mid, properties=None):
        print("publish OK => mid: " + str(mid))

    def on_subscribe(self, client, userdata, mid, granted_qos, properties=None):
        print("Subscribed Ok => mid: " + str(mid) + " " + str(granted_qos))

    def on_message(self, client, userdata, msg):
        self.subscribe_function(str(msg.topic), str(msg.payload))

    def subscribe_function(self, topic, msg):
        print("topic:" + topic + "  msg:" + msg)

    def publish_mqtt(self, topic, msg):
        self.client.publish(topic, payload=msg, qos=1)

    def shutdown(self):
        rospy.loginfo("Stopping image_classifier_and_navigator node")
        self.client.disconnect()
        rospy.loginfo("MQTT client disconnected")
        
if __name__ == '__main__':
    try:
        node = ImageClassifierAndNavigator()
        rospy.on_shutdown(node.shutdown)
    except rospy.ROSInterruptException:
        rospy.loginfo("Exception thrown")
