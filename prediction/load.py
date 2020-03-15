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
INIT_FEATURES = 6
TOTAL_FEATURES = INIT_FEATURES + MAX_CHOICES * (MAX_ITEMS + 1)

conn = mysql.connector.connect(**json.load(open(os.path.join(location, 'mysql-credentials.json'))))
conn.autocommit = True
cursor = conn.cursor()

def getQuestion (questionID):
	out = np.full(shape=TOTAL_FEATURES, fill_value=0.0)

	cursor.execute('SELECT timeStart, timeEnd, minTime, maxTime, answer, confirmation FROM questions WHERE id = {0}'.format(questionID))
	print(cursor.fetchone())
	# cursor.execute('SELECT id, prediction FROM choices WHERE questionID = {0} and valid'.format(questionID))
	# choices = cursor.fetchall()

	# cursor.execute('SELECT num FROM items WHERE choiceID = {0}'.format(choiceID))
	# return np.array([i[0] for i in cursor.fetchall()])
	return out

# for each question
# time start
# time end
# min time
# max time
# answer
# confirmation
# all items and the prediction of all choices

# for the last question
# total set length
# current set length

print(getQuestion(17))