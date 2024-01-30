import tensorflow as tf
from tensorflow import keras
from tensorflow.keras.preprocessing import image
import cv2
import numpy as np

# Load the model from the desktop
model = keras.models.load_model('/home/ayat/Desktop/Kiras/keras_model.h5')

# Load the class labels from the desktop
with open('/home/ayat/Desktop/Kiras/labels.txt', 'r') as file:
    class_labels = [line.strip() for line in file]

# Initialize the camera (camera index 0 for the default camera)
cap = cv2.VideoCapture(0)

while True:
    # Capture a frame from the camera
    ret, frame = cap.read()

    if not ret:
        break

    # Preprocess the frame for prediction
    frame = cv2.resize(frame, (224, 224))  # Resize the frame to match the model's input size
    img_array = image.img_to_array(frame)
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
    cv2.imshow('Real-Time Classification', frame)

    # Break the loop when the 'q' key is pressed
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# Release the camera and close all OpenCV windows
cap.release()
cv2.destroyAllWindows()
