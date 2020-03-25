import numpy as np
import load
import tensorflow as tf

TESTS = 3

trainX = load.getBatchesX()[:-TESTS]
trainY = load.getBatchesY()[:-TESTS]
checkX = load.getBatchesX()[-TESTS:]
checkY = load.getBatchesY()[-TESTS:]

# model = Sequential()
# model.add(LSTM(load.MAX_CHOICES*8, input_shape=(load.TIMESTEPS, load.FEATURES), return_sequences=True))
# model.add(Dropout(0.2))
# model.add(LSTM(64))
# model.add(Dropout(0.2))
# model.add(Dense(16))
# model.add(Dropout(0.2))
# model.add(Dense(load.MAX_CHOICES, activation='sigmoid'))

model = tf.keras.Sequential()
model.add(tf.keras.layers.LSTM(200, input_shape=(load.MAX_QUESTIONS, load.TOTAL_FEATURES), return_sequences=True))
model.add(tf.keras.layers.Dropout(0.2))
model.add(tf.keras.layers.LSTM(200))
model.add(tf.keras.layers.Dense(1000))
model.add(tf.keras.layers.Dropout(0.2))
model.add(tf.keras.layers.Dense(500))
model.add(tf.keras.layers.Dropout(0.2))
model.add(tf.keras.layers.Dense(250))
model.add(tf.keras.layers.Dropout(0.2))
model.add(tf.keras.layers.Dense(250))
model.add(tf.keras.layers.Dropout(0.2))
model.add(tf.keras.layers.Dense(load.MAX_CHOICES, activation='sigmoid'))

print(model.summary())

model.compile(loss='mse', optimizer='adam')

model.fit(trainX, trainY, epochs=300, validation_data=(checkX, checkY), shuffle=True)

model.save('model.h5')