#!/usr/bin/env python
from random import SystemRandom
import hashlib, sys,itertools
import multiprocessing, copy_reg, types
import json

class RC4:

	def __init__(self, message, password):
		self.state = [None]*256
		self.p = None
		self.q = None

		self.message = message
		self.password = [ord(c) for c in password]
		self.setKey()

	def setKey(self):
		key = self.password
		self.state = [n for n in range(256)]
		self.p = self.q = j = 0
		for i in range(256):
			if len(key) > 0:
			   j = (j + self.state[i] + key[i % len(key)]) % 256
			else:
				j = (j + self.state[i]) % 256
			self.state[i], self.state[j] = self.state[j], self.state[i]
	 
	def byteGenerator(self):
		self.p = (self.p + 1) % 256
		self.q = (self.q + self.state[self.p]) % 256
		self.state[self.p], self.state[self.q] = self.state[self.q], self.state[self.p]
		return self.state[(self.state[self.p] + self.state[self.q]) % 256]
	 
	def encrypt(self):
		return [ord(p) ^ self.byteGenerator() for p in self.message]
	 
	def decrypt(self):
		return "".join([chr(c ^ self.byteGenerator()) for c in self.message])

def _pickle_method(m):
    if m.im_self is None:
        return getattr, (m.im_class, m.im_func.func_name)
    else:
        return getattr, (m.im_self, m.im_func.func_name)

copy_reg.pickle(types.MethodType, _pickle_method)

def getRC4From(password, s="", debug=True):
	if(len(s) == 0):
		cg = SystemRandom()
		s  = "".join([unichr(cg.randrange(32,126)) for i in range(16)])

	h0   = hashlib.md5(password.encode('utf-8')).hexdigest()
	md5t = "".join([unichr(ord(c)) for c in h0[:10]])#
	ib   = 16 * (md5t + s) 
	h1   = hashlib.md5(ib.encode('utf-8')).hexdigest()
	h1th = h1[:8] + "00"*4
	print h1th
	hf   = hashlib.md5(h1th.encode('utf-8')).hexdigest()
	return [s, hf]

def getRC4FromFromKey(password, s="", debug=True):
	h1th = password
	hf   = hashlib.md5(h1th.encode('utf-8')).hexdigest()
	return [s, hf]

def encrypt(text, password):
	crypter = RC4(text, password)
	enc     = crypter.encrypt()
	enc     = "".join([unichr(c) for c in enc])
	return enc

def leCrypt(text, password, debug=False):
	theHeader		 = {'s': '', 'v':'', 'vH':''}
	
	cg = SystemRandom()
	v  =  "".join([unichr(cg.randrange(32,126)) for i in range(16)]) #16 bytes
	v  = v.encode("utf-8")

	(s, key)         = getRC4From(password, debug=True)	
	theHeader['s']   = s
	theHeader['v']   = encrypt(v, key)
	theHeader['vH']  = encrypt(hashlib.md5(v).digest(), key)
	theHeader['enc'] = encrypt(text, key)
	if debug:
		print theHeader

	return theHeader

def decrypt(enc, password):
	enc     = [ord(c) for c in list(enc)]
	crypter = RC4(enc, password)
	dec     = crypter.decrypt()
	return dec

def verifyKey(header, key):
	dv  = decrypt(header['v'], key)
	dvH = decrypt(header['vH'], key)
	return hashlib.md5(dv).digest() == dvH

def officeDecrypt(header, password):
	(s, key)   = getRC4From(password, header['s'])

	if verifyKey(header, key):
		return decrypt(header['enc'], key)
	return False

def officeDecryptWithKey(header, key):
	(s, key) = getRC4FromFromKey(key, header['s'])
	if verifyKey(header, key):
		return decrypt(header['enc'], key)
	return False

#header = leCrypt("DCTF{69e0f71f25ece4351e4d73af430bec43}", "this-shit-is-impossible-to-hack4", debug=True)
#print header
#fh = open('challenge.txt', 'w')
#fh.write(json.dumps(header))
#fh.close()
fh = open('challenge.txt', 'r')
header = json.loads(fh.read())
fh.close()
print officeDecrypt(header, "this-shit-is-impossible-to-hack4")
print officeDecryptWithKey(header, '2307ca0d00000000')