import load
import json

import mysql.connector

import plaidml.keras
plaidml.keras.install_backend()

from keras.models import load_model
model = load_model('predictback/model.h5')

import zerorpc

conn = mysql.connector.connect(**json.load(open('predictback/mysql-credentials.json')))
conn.autocommit = True
cursor = conn.cursor()

class RPCServer (object):
	def get (self, setID, ind):
		setID = int(setID)
		ind = int(ind)
		print(setID, ind)
		batch = load.getBatchX(setID, ind)
		if batch is not None:
			cursor.execute('SELECT COUNT(choice) FROM predictions WHERE setID = '+str(setID)+' AND ind = '+str(ind))
			if cursor.fetchone()[0] == 0:
				for i, v in enumerate(model.predict(batch)[0]):
					cursor.execute('INSERT INTO predictions VALUES ('+str(setID)+', '+str(ind)+', '+str(i)+', '+str(v)+')')
			return 0
		else:
			return 1


server = zerorpc.Server(RPCServer())
server.bind('tcp://0.0.0.0:4343')
server.run()