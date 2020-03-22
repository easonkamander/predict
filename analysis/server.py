import numpy as np
# import load
# import tensorflow as tf
from xmlrpc.server import SimpleXMLRPCServer
from xmlrpc.server import SimpleXMLRPCRequestHandler

# model = tf.keras.models.load_model('model.h5')

with SimpleXMLRPCServer(('localhost', 8000)) as server:
	# def analyze (setID, batchFill):
	# 	load.refresh()
	# 	batchX = load.getBatchX(setID, batchFill)
	# 	if batchX is not None:
	# 		batchY = model.predict(batchX)
	# 		load.setBatchY(setID, batchFill, batchY)
	# server.register_function(analyze, 'analyze')

	def method (a, b, c):
		print(a, b, c)
		return 'aaaaaaaa'
	server.register_function(method, 'method')

	server.serve_forever()