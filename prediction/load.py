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

def getItem (itemID):
	cursor.execute("SELECT num FROM items WHERE id = "+str(itemID))
	rawResult = cursor.fetchone()
	return rawResult[0] if rawResult != None else None

print(getItem(10))
print(getItem(10000))