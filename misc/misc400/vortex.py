#!/usr/bin/python

import sys, re, json, base64, time

config = {
	'NAME': 'VORTEX AC',
	'COMMAND': 0x13,
	'HEADER': '3100 1600',
	'WAVES': {'HIGH':'350', 'BREAK':'550', 'LOW':'1150'},
	'PAYLOAD': '001110110010110010011011011111111111111111XX1011XXXX1111XXX01111XXXXXXX1XXXXXXXX111111111111111111111111XXXXXXXX',
	'POSITIONS': {
		'AC_ON_OFF'		: [0x2A, 0x1], #1 bit changed
		'TIMER_ON_OFF'	: [0x2B, 0x1], # 1 bit changed
		'MODES'			: [0x30, 0x4], #bits changed
		'TEMPERATURE'	: [0x38, 0x4], #4bits changed from 0 to 15 aka from 16 to 31 degrees
		'FAN'			: [0x40, 0x2], #3 bits changed
		'SLEEP_ON_OFF'	: [0x42, 0x1], #1 bit changed
		'SWING_ON_OFF'	: [0x43, 0x3], #3bits changed
		'TIMER_DETAILS'	: [0x46, 0xA], #10 bits changed
		'CHECKSUM'		: [0x68, 0x8], #8bits length		
	},
	'VALUES': {
		'AC_ON_OFF': {
			'ON': '0',
			'OFF': '1'
		},
		'TIMER_ON_OFF': {
			'ON': '0',
			'OFF': '1'
		},
		'TIMER_DETAILS': {
			#todo
		},
		'MODES': {
			'FEEL': '1110',
			'COOL': '0011',
			'DRY': '1011',
			'FAN': '0001',
			'HEAT': '0111'
		},
		'TEMPERATURE': {
			# bin(degrees-16)[2:] + zeroes to make it 4 bits long
		},
		'FAN': {
			'AUTO': '11',
			'LOW': '10',
			'MID': '00',
			'HIGH': '01'
		},
		'SLEEP_ON_OFF': {
			'ON': '1',
			'OFF': '0' #it goes back to last fan
		},
		'SWING_ON_OFF': {
			'OFF': '111',
			'ON': '000'
		},
		'CHECKSUM': {

		}
	}
}

class AC:
	def __init__(self, config, settings = {}):
		self.config = config
		self.settings = settings
		self.reset()
		self.getPayload()
		self.initDefault()

	def reset(self):
		self.payload = list(self.config['PAYLOAD'])

	def initDefault(self):

		self.turnAC(self.settings['AC'])
		self.turnTimer(self.settings['TIMER_TIME'], self.settings['TIMER_STATE'])
		self.turnMode(self.settings['MODE'])
		self.changeTemperatureTo(self.settings['TEMPERATURE'])
		self.turnFAN(self.settings['FAN'])
		self.turnSleep(self.settings['SLEEP'])
		self.turnSwing(self.settings['SWING'])

		return self.getPayload()

	def isConfigPosition(self, mode):
		if mode not in self.config['POSITIONS']:
			return False
		return True

	def getConfigPosition(self, mode):
		return self.config['POSITIONS'][mode]

	def writeToPayload(self, mode='AC_ON_OFF', key='ON'):
		if not self.isConfigPosition(mode):
			#print "[*] Please define mode ", mode
			return False

		(pos, length) = self.getConfigPosition(mode)

		if key in self.config['VALUES'][mode]:
			value = self.config['VALUES'][mode][key]
		else:
			#print key, mode
			value = key		

		for i in range(length):
			self.payload[pos] = value[i]
			pos+=1


		return True

	def readFromPayload(self, mode='AC_ON_OFF'):
		if not self.isConfigPosition(mode):
			#print "[*] Please define mode ", mode
			return False

		(pos, length) = self.getConfigPosition(mode)
		data = ""
		for i in range(length):
			data+= self.payload[pos]
			pos+=1

		return data

	def getModeValue(self, mode='AC_ON_OFF', rawv = ''):
		if mode in self.config['VALUES']:
			for k in self.config['VALUES'][mode]:
				if self.config['VALUES'][mode][k] == rawv:
					return k
		return False

	def turnAC(self, mode = 'ON'):
		self.writeToPayload('AC_ON_OFF', mode)

	def getAC(self):
		rawvalue = self.readFromPayload('AC_ON_OFF')
		return self.getModeValue('AC_ON_OFF', rawvalue)

	def turnTimer(self, time = 1.5, mode = 'OFF'):
		self.writeToPayload('TIMER_ON_OFF', mode)
		#todo timing value
		self.writeToPayload('TIMER_DETAILS', '1111111111')

	def getTimerStatus(self):
		rawvalue = self.readFromPayload('TIMER_ON_OFF')
		return self.getModeValue('TIMER_ON_OFF', rawvalue)

	def getTimerDetails(self):
		rawvalue = self.readFromPayload('TIMER_DETAILS')
		return rawvalue

	def turnMode(self, mode):
		self.writeToPayload('MODES', mode)

	def getMode(self):
		rawvalue = self.readFromPayload('MODES')
		return self.getModeValue('MODES', rawvalue)

	def changeTemperatureTo(self, degrees=18):
		degrees = str(bin(int(degrees)-16)[2:])[::-1]
		degrees = degrees + ('0'*(4-len(degrees))) #append missing bits
		self.writeToPayload('TEMPERATURE', degrees)

	def getTemperature(self):
		rawvalue = self.readFromPayload('TEMPERATURE')
		degrees  = str(int(rawvalue[::-1],2)+16)
		return degrees

	def turnFAN(self, mode):
		self.writeToPayload('FAN', mode)

	def getFAN(self):
		rawvalue = self.readFromPayload('FAN')
		return self.getModeValue('FAN', rawvalue)

	def turnSleep(self, mode = "ON"):
		self.writeToPayload('SLEEP_ON_OFF', mode)

	def getSleep(self):
		rawvalue = self.readFromPayload('SLEEP_ON_OFF')
		return self.getModeValue('SLEEP_ON_OFF', rawvalue)

	def turnSwing(self, mode = "ON"):
		self.writeToPayload('SWING_ON_OFF', mode)

	def getSwing(self):
		rawvalue = self.readFromPayload('SWING_ON_OFF')
		return self.getModeValue('SWING_ON_OFF', rawvalue)

	def computeChecksum(self):
		(pos, length) = self.getConfigPosition('CHECKSUM')
		if 'X' in self.payload[0:pos] or 'X' in self.payload[pos+length:]:
			#print "[*] Some settings are missing."
			return False

		payload = self.payload[0:pos] + self.payload[pos+length:]
		bs = re.findall('........?', "".join(payload))
		cs = 0
		for byte in bs:
			byte = int(byte[::-1], 2)
			cs  += byte
		
		cs &=0xFF
		cs += 0xC
		cs = str(bin(cs)[2:][::-1])
		return cs + "0"*(8-len(cs))

	def checksum(self, cs=None):
		(pos, length) = self.getConfigPosition('CHECKSUM')

		if cs == None and 'X' in self.payload[pos:pos+length]:
			#print "[*] Checksum not initialised."
			return False

		if cs == None:
			return "".join(self.payload[pos:pos+length])
		else:
			self.writeToPayload('CHECKSUM', cs)
			return True


	def getPayload(self):
		cs = self.computeChecksum()
		if cs != False and self.checksum() != cs:
			self.checksum(cs)

		return "".join(self.payload)

	def getPacket(self):
		payload = list(self.getPayload())
		newpayload = []
		for p in payload:
			if p == '1':
				p = self.config['WAVES']['HIGH']
			elif p == '0':
				p = self.config['WAVES']['LOW']

			newpayload.append(p)
		
		payload = (" "+self.config['WAVES']['BREAK']+" ").join(newpayload)

		payload = self.config['HEADER'] + " " + self.config['WAVES']['BREAK'] + " " + payload + " " + self.config['WAVES']['BREAK']
		return payload

	def getRawPacket(self, packet):		
		#for step in range(times):
			#self.i2c.sendCommand(self.config['COMMAND'], list(packet))
		#	time.sleep(1.5)
		return [self.config['COMMAND']] + list(packet)


	def isLength(self):
		return len(self.payload) == 112

	
	def packetValid(self):

		(pos, length) = self.getConfigPosition('CHECKSUM')
		cs = "".join(self.payload[pos:pos+length])

		#cs            = "".join(self.payload[pos:pos+length])
		#print cs, self.computeChecksum()
		return self.computeChecksum() == cs

	def filterSignal(self, rawdata, init=False):
		rawdata = " ".join(rawdata.split(" ")[2:]) #delete header
		data = self.normalize(rawdata)
		data = self.toBinary(data)

		if init == True:
			self.payload = data
		#print self.payload
		return data

	def normalize(self, data):
		newline = []
		data = filter(None, data.strip().split(" "))
		for nr in data:
			nr = int(nr)
			if(nr%100>=50):
				nr=nr-nr%100+100
			else:
				nr=nr-nr%100

			if abs(nr-int(self.config['WAVES']['LOW'])) <= 100:
				nr = int(self.config['WAVES']['LOW'])

			if abs(nr-int(self.config['WAVES']['HIGH'])) <= 100:
				nr = int(self.config['WAVES']['HIGH'])
			
			if abs(nr-int(self.config['WAVES']['BREAK'])) <= 100:
				nr = int(self.config['WAVES']['BREAK'])

			newline.append(str(nr))
		return " ".join(newline)

	def toBinary(self, data):
		data = data.replace(str(self.config['WAVES']['LOW']),"0")
		data = data.replace(str(self.config['WAVES']['HIGH']), "1")
		data = data.replace(" ","")
		data = data.replace(str(self.config['WAVES']['BREAK']), " ")
		data = data.replace("\n","")
		
		return "".join(data.split(" ")[1:])

	def makeConfig(self):
		settings = {
			'AC':self.getAC(),
			'TIMER_TIME':self.getTimerDetails(),
			'TIMER_STATE':self.getTimerStatus(),
			'MODE':self.getMode(),
			'TEMPERATURE':self.getTemperature(),
			'FAN':self.getFAN(),
			'SLEEP':self.getSleep(),
			'SWING':self.getSwing()
		}

		return settings



settings = {
	'AC':'OFF',
	'TIMER_TIME':'1111111111',
	'TIMER_STATE':'OFF',
	'MODE':'FEEL',
	'TEMPERATURE':'16',
	'FAN':'LOW',
	'SLEEP':'OFF',
	'SWING':'OFF'
}



ac       = AC(config, settings)
#packet   = ac.getPacket() 
#print packet
#print json.dumps(settings)

if (len(sys.argv) != 3):
	sys.exit(0)

action = sys.argv[1]
packet = sys.argv[2]


if action == 'config': #get config settings object from raw

	ac.filterSignal(base64.b64decode(packet).strip(), True)
	if ac.isLength() == False:
		print "13338" #invalid length
		sys.exit(0)
	
	if ac.packetValid() == False:
		print "13337" #invalid packet
		sys.exit(0)

	print json.dumps(ac.makeConfig())
elif action == 'raw': #get raw from confing settings object
	settings = base64.b64decode(packet).strip()
	obj      = json.loads(settings)
	ac       = AC(config, obj)
	packet   = ac.getPacket() 
	print packet
#print 'error, you might want to consider contacting us if you see to many times'