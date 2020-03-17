import zerorpc
import sys

if len(sys.argv) == 3 and sys.argv[1].isnumeric() and sys.argv[2].isnumeric():
	client = zerorpc.Client()
	client.connect("tcp://127.0.0.1:4343")
	client.get(int(sys.argv[1]), int(sys.argv[2]))