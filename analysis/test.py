import numpy as np
import threading
import load
import tensorflow as tf
from xmlrpc.server import SimpleXMLRPCServer
from xmlrpc.server import SimpleXMLRPCRequestHandler

model = tf.keras.models.load_model('model.h5')

def analysisProcess (setID, batchFill):
	batchX = load.getBatchX(setID, batchFill)
	if batchX is not None:
		batchY = model.predict(batchX)[0]
		load.setBatchY(setID, batchFill, batchY)

with SimpleXMLRPCServer(('localhost', 8000)) as server:
	def analysisRequest (setID, batchFill):
		analysisThread = threading.Thread(target=analysisProcess, args=(setID, batchFill))
		analysisThread.start()
		return 0
	server.register_function(analysisRequest, 'analysisRequest')

	server.serve_forever()