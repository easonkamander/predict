import numpy as np
import load

import plaidml.keras
plaidml.keras.install_backend()

from keras.models import Sequential
from keras.layers import Dense, Dropout, LSTM, Flatten, Activation
from keras.optimizers import Adam

print('Loading...', end='')

TESTS = 2

trainX = load.getBatchesX()[:-TESTS]
trainY = load.getBatchesY()[:-TESTS]
checkX = load.getBatchesX()[-TESTS:]
checkY = load.getBatchesY()[-TESTS:]

print('\rLoading...Done')

# model = Sequential()
# model.add(LSTM(load.MAX_CHOICES*8, input_shape=(load.TIMESTEPS, load.FEATURES), return_sequences=True))
# model.add(Dropout(0.2))
# model.add(LSTM(64))
# model.add(Dropout(0.2))
# model.add(Dense(16))
# model.add(Dropout(0.2))
# model.add(Dense(load.MAX_CHOICES, activation='sigmoid'))

print(trainY)

model = Sequential()
model.add(LSTM(20, input_shape=(load.TIMESTEPS, load.FEATURES)))
model.add(Dense(100))
model.add(Dropout(0.2))
model.add(Dense(50))
model.add(Dropout(0.2))
model.add(Dense(25))
model.add(Dropout(0.2))
model.add(Dense(load.MAX_CHOICES, activation='sigmoid'))

print(model.summary())

model.compile(loss='mse', optimizer='adam')

model.fit(trainX, trainY, epochs=200, validation_data=(checkX, checkY), shuffle=True)

model.save('predictback/model.h5')