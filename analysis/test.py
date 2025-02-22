#!/usr/bin/python3 -u
import numpy as np
import threading
import load
import time
import os
from tensorflow.keras.models import load_model
from xmlrpc.server import SimpleXMLRPCServer
from xmlrpc.server import SimpleXMLRPCRequestHandler

model = load_model(os.path.join(load.location, 'models', 'main.h5'))

def analysisProcess (setID, batchFill):
	batchX = load.getBatchX(setID, batchFill)
	tstart = time.time()
	while batchX is None and time.time() - tstart < 3:
		time.sleep(0.5)
		batchX = load.getBatchX(setID, batchFill)
	if batchX is not None:
		batchY = model.predict(batchX)[0]
		load.setBatchY(setID, batchFill, batchY)

with SimpleXMLRPCServer(('localhost', 8031)) as server:
	def analysisRequest (setID, batchFill):
		analysisThread = threading.Thread(target=analysisProcess, args=(setID, batchFill))
		analysisThread.start()
		return 0
	server.register_function(analysisRequest, 'analysisRequest')

	server.serve_forever()