import mysql.connector
import numpy as np
import json
import os

location = os.path.realpath(
	os.path.join(
		os.getcwd(),
		os.path.dirname(__file__),
		'..'
	)
)

MAX_QUESTIONS = 12
MAX_CHOICES = 8
MAX_ITEMS = 2
MAX_ITEM_BITS = 10
MAX_TIME = 8
META_FEATURES = 6
TOTAL_FEATURES = META_FEATURES + MAX_CHOICES * (MAX_ITEMS + 1)

conn = mysql.connector.connect(**json.load(open(os.path.join(location, 'mysql-credentials.json'))))
conn.autocommit = True
cursor = conn.cursor()

def getBatchX (setID, batchFill):
	out = np.full(shape=(1, MAX_QUESTIONS, TOTAL_FEATURES), fill_value=0.0)

	timeFrame = None

	cursor.execute('SELECT setLen FROM sets WHERE id = {0}'.format(setID))
	resSetLen = cursor.fetchone()[0]

	for setInd in range(batchFill):
		cursor.execute('SELECT timeStart, timeEnd, minTime, maxTime, confirmation, answer, id FROM questions WHERE setID = {0} AND setInd = {1} ORDER BY id DESC'.format(setID, setInd))
		resMeta = cursor.fetchone()

		if resMeta != None:
			if timeFrame:
				out[0, MAX_QUESTIONS - batchFill + setInd, 0] = 2 * np.arctan(resMeta[0].timestamp() - timeFrame) / np.pi
			else:
				out[0, MAX_QUESTIONS - batchFill + setInd, 0] = -1 # (resMeta[0].timestamp() - resMeta[0].timestamp().replace(hour=0, minute=0, second=0, microsecond=0)).total_seconds() / 86400

			out[0, MAX_QUESTIONS - batchFill + setInd, 1] = 2 * np.arctan(resMeta[1].timestamp() - resMeta[0].timestamp()) / np.pi
			out[0, MAX_QUESTIONS - batchFill + setInd, 2] = (resMeta[2] + 1) / MAX_TIME
			out[0, MAX_QUESTIONS - batchFill + setInd, 3] = (resMeta[3] + 1) / MAX_TIME
			out[0, MAX_QUESTIONS - batchFill + setInd, 4] = (2 if resMeta[4] == 'full' else 1 if resMeta[4] == 'single' else 0) / 2.0

			if resMeta[5]:
				out[0, MAX_QUESTIONS - batchFill + setInd, 5] = (resMeta[5] + 1) / MAX_CHOICES
			else:
				out[0, MAX_QUESTIONS - batchFill + setInd, 5] = 2 * np.arctan(setInd + setInd / resSetLen) / np.pi

			cursor.execute('SELECT id, prediction FROM choices WHERE questionID = {0} and valid'.format(resMeta[6]))
			resChoices = cursor.fetchall()

			for i, choice in enumerate(resChoices):
				if choice[1] != None:
					out[0, MAX_QUESTIONS - batchFill + setInd, META_FEATURES + i * (MAX_CHOICES + 1)] = choice[1]

				cursor.execute('SELECT num FROM items WHERE choiceID = {0}'.format(choice[0]))
				resItems = cursor.fetchall()

				for j, item in enumerate(resItems):
					out[0, MAX_QUESTIONS - batchFill + setInd, META_FEATURES + i * (MAX_CHOICES + 1) + j + 1] = (item[0] + 1) / 2 ** MAX_ITEM_BITS

	return out

# for each question
# time start
# time end
# min time
# max time
# confirmation
# answer
# all items and the prediction of all choices

# for the last question
# total set length
# current set length

print(getBatchX(4, 1)[0])
print(getBatchX(4, 1)[0][-1])
print(getBatchX(4, 4)[0])
print(getBatchX(4, 4)[0][-1])
print(getBatchX(4, 5)[0])
print(getBatchX(4, 5)[0][-1])