import rospy
from sensor_msgs.msg import Image
from cv_bridge import CvBridge
import cv2
import numpy as np
from tensorflow import keras
from keras.preprocessing import image

# Load the model from the desktop
model = keras.models.load_model('/home/raghad/turtlebot2_ws/src/my_kinect_stream/keras_model.h5')

# Compile the model (you can adjust the optimizer, loss, and metrics based on your original compilation)
model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])

# Load the class labels from the desktop
with open('/home/raghad/turtlebot2_ws/src/my_kinect_stream/labels.txt', 'r') as file:
    class_labels = [line.strip() for line in file]

# Initialize the CvBridge
bridge = CvBridge()

# Define a fixed region of interest (ROI) within the image
roi_x = 100  # X-coordinate of the top-left corner of the ROI
roi_y = 100  # Y-coordinate of the top-left corner of the ROI
roi_width = 224  # Width of the ROI
roi_height = 224  # Height of the ROI

# ROS Subscriber for the Kinect RGB image
def image_callback(msg):
    try:
        # Convert the ROS Image message to an OpenCV image
        frame = bridge.imgmsg_to_cv2(msg, desired_encoding="bgr8")

        # Extract the fixed region of interest (ROI) from the image
        roi = frame[roi_y:roi_y + roi_height, roi_x:roi_x + roi_width]

        # Preprocess the ROI for prediction
        roi = cv2.resize(roi, (224, 224))
        img_array = image.img_to_array(roi)
        img_array = np.expand_dims(img_array, axis=0)
        img_array = img_array / 255.0  # Normalize the image data

        # Make a prediction using the loaded model
        predictions = model.predict(img_array)

        # Get the class with the highest probability
        predicted_class_index = np.argmax(predictions[0])
        predicted_class = class_labels[predicted_class_index]

        # Print the predicted class
        print(f"Predicted class: {predicted_class}")

        # Display the frame with the predicted class name
        cv2.putText(frame, f" {predicted_class}", (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)

        # Show the processed image (optional)
        cv2.imshow("Processed Image", frame)
        cv2.waitKey(1)

    except Exception as e:
        print(f"Error processing image: {e}")

def main():
    rospy.init_node('image_classifier_node', anonymous=True)

    # ROS Subscriber for the Kinect RGB image
    rospy.Subscriber('/camera/rgb/image_raw', Image, image_callback)

    rospy.spin()

if __name__ == "__main__":
    main()
