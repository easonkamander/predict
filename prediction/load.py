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

TIMESTEPS = 12
MAX_CHOICES = 8
MAX_ITEMS = 3
MAX_ITEM_BITS = 10
MAX_TIME = 8
INIT_FEATURES = 11
FEATURES = INIT_FEATURES + MAX_CHOICES * MAX_ITEMS

conn = mysql.connector.connect(**json.load(open(os.path.join(location, 'mysql-credentials.json'))))
conn.autocommit = True
cursor = conn.cursor()

def getQuestion (questionID):
	out = np.fill(shape=FEATURES, fill_value=0)
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
# all items and the prediction of all choices

# for the last question
# total set length
# current set length

print(getQuestion(None))