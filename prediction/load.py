import mysql.connector
import numpy as np
import json
import os

__location__ = os.path.realpath(
	os.path.join(
		os.getcwd(),
		os.path.dirname(__file__)
	)
)

TIMESTEPS = 12
MAX_CHOICES = 16
MAX_ITEMS = 1
MAX_ITEM_BITS = 10
MAX_TIME = 8
INIT_FEATURES = 11
FEATURES = INIT_FEATURES + MAX_CHOICES * MAX_ITEMS

conn = mysql.connector.connect(**json.load(open(os.path.join(__location__, '../mysql-credentials.json'))))
conn.autocommit = True
cursor = conn.cursor()

def getBatchX (setID, ind):
	out = np.full(shape=(1, TIMESTEPS, FEATURES), fill_value=0.0)

	cursor.execute("SELECT setLen FROM sets WHERE id = "+str(setID))
	setLen = cursor.fetchone()[0]

	cursor.execute("SELECT timeStart FROM sets WHERE id = "+str(setID))
	timeFrame = cursor.fetchone()[0].timestamp()

	for setInd in range(ind):
		cursor.execute("SELECT * FROM questions WHERE setID = "+str(setID)+" AND setInd = "+str(setInd)+" ORDER BY id DESC LIMIT 1")
		question = cursor.fetchone()

		out[0, TIMESTEPS - ind + setInd, 0] = setLen / TIMESTEPS
		out[0, TIMESTEPS - ind + setInd, 1] = (setInd + 1) / TIMESTEPS
		out[0, TIMESTEPS - ind + setInd, 2] = (question[4] + 1) / MAX_CHOICES if question[4] and setInd + 1 < ind else 0
		out[0, TIMESTEPS - ind + setInd, 3] = 2 * np.arctan(question[2].timestamp() - timeFrame) / np.pi
		out[0, TIMESTEPS - ind + setInd, 4] = 2 * np.arctan(question[3].timestamp() - question[2].timestamp()) / np.pi
		out[0, TIMESTEPS - ind + setInd, 5] = (question[6] + 1) / MAX_CHOICES
		out[0, TIMESTEPS - ind + setInd, 6] = (question[7] + 1) / MAX_ITEMS
		out[0, TIMESTEPS - ind + setInd, 7] = (question[8] + 1) / MAX_ITEM_BITS
		out[0, TIMESTEPS - ind + setInd, 8] = (question[9] + 1) / MAX_TIME
		out[0, TIMESTEPS - ind + setInd, 9] = (question[10] + 1) / MAX_TIME
		out[0, TIMESTEPS - ind + setInd, 10] = (3 if question[11] == 'full' else 2 if question[11] == 'single' else 1 if question[11] == 'none' else 0) / 3

		timeFrame = question[3].timestamp()

		cursor.execute("SELECT * FROM items WHERE questionID = "+str(question[1]))
		items = cursor.fetchall()

		for item in items:
			out[0, TIMESTEPS - ind + setInd, INIT_FEATURES + MAX_ITEMS * item[1] + item[2]] = (item[3] + 1) / 2**MAX_ITEM_BITS

	return out

def getBatchY (setID, ind):
	out = np.full(shape=(1, MAX_CHOICES), fill_value=0.0)

	cursor.execute("SELECT answer FROM questions WHERE setID = "+str(setID)+" AND setInd = "+str(ind - 1)+" ORDER BY id DESC LIMIT 1")
	answer = cursor.fetchone()[0]
	if answer is not None:
		out[0, answer] = 1.0

	return out

def getBatchesX ():
	out = np.empty((0, TIMESTEPS, FEATURES))

	cursor.execute("SELECT id, setLen FROM sets WHERE setInd = setLen")
	sets = cursor.fetchall()

	for i in sets:
		for j in range(i[1]):
			out = np.concatenate((out, getBatchX(i[0], j + 1)))

	return out

def getBatchesY ():
	out = np.empty((0, MAX_CHOICES))

	cursor.execute("SELECT id, setLen FROM sets WHERE setInd = setLen")
	sets = cursor.fetchall()

	for i in sets:
		for j in range(i[1]):
			out = np.concatenate((out, getBatchY(i[0], j + 1)))

	return out