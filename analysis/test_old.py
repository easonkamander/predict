import numpy as np
import load
import tensorflow as tf

model = tf.keras.models.load_model('model.h5')

print(model.predict(load.getBatchX(10, 1)))