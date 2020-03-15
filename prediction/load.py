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

def getChoice (choiceID):
	cursor.execute("SELECT num FROM items WHERE choiceID = ?")
	res = cursor.fetchall()
	if res == None:
		return None
	else:
		return [i[0] for i in res]

print(getChoice(10))
print(getChoice(10000))