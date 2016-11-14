#!/usr/bin/python
import urllib2, time, re
import requests, json
import base64, subprocess

#you need vortex.py in the same folder
url       = 'https://msc400.dctf-finals16.def.camp'
task      = url + 'infrared.php?action=sendIRRawCommand&packet='

s = requests.session()

def get(link, times = 1):
	c = ""
	for i in range(times):
		#print link
		c += s.get(link).text
		#time.sleep(.1)
	return c

def getTask(response):
	todo = dict()

	m = re.search('\([\w\W]+\)', response, re.MULTILINE)
	task = m.group(0).replace("(\n","").replace("\n)","").split("\n")
	for t in task:
		t = t.strip().replace("[","").replace("]","")
		t = t.encode('UTF-8').split(" => ")
		todo[t[0]] = t[1]
	return todo

def getDigital(todo):
	todo = base64.b64encode(json.dumps(todo))
	p = subprocess.Popen(['python', 'vortex.py','raw', todo], 
						  stdout=subprocess.PIPE, 
						  stderr=subprocess.PIPE)
	out, err = p.communicate()
	return out

def rawToDigital(raw, freq = 40000):
	freq = int(1./min(freq, 100000)*(10**6))

	digital = []
	now     = '-'# HIGH, always start with high
	total   = 0

	for i in range(len(raw)):
		if(raw[i] != now):
			newnr   = total*freq
			digital.append(str(newnr))
			total   = 1
			now     = raw[i]
		else:
			total   += 1

	digital.append(str(total*freq))

	return " ".join(digital)

def digitalToRaw(digital, freq = 40000):
	freq = int(1./min(freq, 100000)*(10**6))	

	digits = digital.strip().split(" ")
	raw    = ''
	pulse  = 1

	for d in digits:
		char = '_' #LOW
		if pulse:
			char = '-' #HIGH

		raw += str(char) * int(int(d)/freq)
		pulse = not pulse

	return raw# + '_'*((1150/freq)*5)

count = 0
for count in range(110): 
	todo    = getTask(get(task))
	digital = getDigital(todo)
	#print digital
	raw     = digitalToRaw(digital)
	#print rawToDigital(raw)
	answer  = get(task + raw)
	if answer.find("DCTF") != -1:
		print answer
		break
	print count  