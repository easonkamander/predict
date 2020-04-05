import numpy as np
import load
import random
import os
from tensorflow.keras import Sequential
from tensorflow.keras.layers import LSTM, Dropout, Dense

trainX = load.getBatchesX()
trainY = load.getBatchesY()

tests = random.sample(range(len(trainX)), 2)

checkX = trainX[tests]
checkY = trainY[tests]

trainX = np.delete(trainX, tests, 0)
trainY = np.delete(trainY, tests, 0)

print(trainX.shape)
print(trainY.shape)
print(checkX.shape)
print(checkY.shape)

model = Sequential()
model.add(LSTM(150, input_shape=(load.MAX_QUESTIONS, load.TOTAL_FEATURES), return_sequences=True))
model.add(Dropout(0.15))
model.add(Dense(150))
model.add(Dropout(0.15))
model.add(LSTM(120))
model.add(Dropout(0.15))
model.add(Dense(800))
model.add(Dropout(0.2))
model.add(Dense(600))
model.add(Dropout(0.2))
model.add(Dense(400))
model.add(Dropout(0.2))
model.add(Dense(200))
model.add(Dropout(0.2))
model.add(Dense(load.MAX_CHOICES, activation='softmax'))

print(model.summary())

model.compile(loss='rmse', optimizer='adam')

model.fit(trainX, trainY, epochs=100, validation_data=(checkX, checkY), shuffle=True)

model.save(os.path.join(load.location,'models', 'main.h5'))