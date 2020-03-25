import numpy as np
import load
import tensorflow as tf
from xmlrpc.server import SimpleXMLRPCServer
from xmlrpc.server import SimpleXMLRPCRequestHandler

model = tf.keras.models.load_model('model.h5')

with SimpleXMLRPCServer(('localhost', 8000)) as server:
	def analyze (setID, batchFill):
		load.refresh()
		batchX = load.getBatchX(setID, batchFill)
		if batchX is not None:
			batchY = model.predict(batchX)[0]
			print(setID, batchFill, batchY)
			load.setBatchY(setID, batchFill, batchY)
			return 0
	server.register_function(analyze, 'analyze')

	server.serve_forever()