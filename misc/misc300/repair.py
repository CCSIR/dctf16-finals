#!/usr/bin/python
import PIL.Image
import imageio, numpy,re,sys, os,shutil, time

framesDir = '/home/andrei/Downloads/frames/'
now       = 0
nowBit    = 0

#get original
fh = open('biti.txt','r')
expBits = fh.read()
fh.close()

fh = open('imagini-tip.txt', 'r')
cachedImages = list(fh.read())
fh.close()

#get frames
files = sorted(os.listdir(framesDir))
nf = len(files)

def crop(image, startx, stopx, starty, stopy):
	#nim   = numpy.zeros((stopx-startx,stopy-starty,3), dtype=numpy.uint8)
	image = numpy.array(image)
	nim = image[startx:stopx, starty:stopy]
	#for i in range(startx, stopx):
	#	nim[i-startx] = image[i][starty:stopy]
		#for j in range(starty, stopy):
			#print(image[i][j])
			#nim[i-startx][j-starty]=image[i][j]
	return nim

def countCorrectFrameFromCacheBy(b):
	global now, cachedImages
	cnt = 0
	while now < nf:
		#print cachedImages[now], b
		if cachedImages[now] == b:
			cnt+=1
			now+=1
		else:
			break

	return cnt

def countCorrectFrameBy(b):
	global now, framesDir, files

	cnt = 0
	while now < nf:
		#start = time.time()
		im = imageio.imread(framesDir + files[now])
		#print framesDir + files[now]
		#1080x1920
		im=crop(im, 690, 730, 610, 650)
		#im = PIL.Image.fromarray(im, 'RGB')
		#im.save("test.jpg")
		#sys.stdout.write('.')
		#sys.stdout.flush()
		#print time.time()-start
		#sys.exit()
		if(int(im.mean()) < 100):
			t = 0
		else:
			t = 1
		fh.write(str(t))

		#if it's what we expect, continue and increment
		if t == b:
			cnt +=1
			now +=1
		else:
			now +=1
			return cnt

def addFrames(cnt):
	global files, framesDir

	fname     = ord('a')
	goodframe = files[now-1]
	for i in range(cnt):
		print "Adding ",goodframe.split(".")[0] + chr(fname) + ".jpg"
		shutil.copy2(framesDir + goodframe, framesDir + goodframe.split(".")[0] + chr(fname) + ".jpg")
		fname +=1

def removeFrames(cnt):
	global files, framesDir
	#print cachedImages[now]
	for i in range(cnt):
		print "Remove: ", files[now-1-i], cachedImages[now-1-i]	
		os.remove(framesDir + files[now-1-i])

def checkAllImages():
	global files
	fh = open('imagini.tip', 'w')
	cnt = 0
	for f in files:
		im = imageio.imread(framesDir + f)
		im=crop(im, 690, 730, 610, 650)
		if(int(im.mean()) < 100):
			t = 0
		else:
			t = 1
		fh.write(str(t))
		cnt+=1
		sys.stdout.write("\r" + str(cnt))
		sys.stdout.flush()
		#print "\r", cnt,
	fh.close()

def recoverFile():
	global files
	fh  = open('recovered.txt', 'w')
	cnt = 0
	typeNow = 1
	cntType = 0
	for f in files:
		#print ".",
		im = imageio.imread(framesDir + f)
		im=crop(im, 690, 730, 610, 650)
		if(int(im.mean()) < 100):
			t = 0
		else:
			t = 1
		
		if typeNow == t:
			cntType += 1
		else:
			repeats = cntType/8.
			originalr = repeats
			if(repeats - int(repeats) >= .6):
				repeats = int(repeats) + 1
			else:
				repeats = int(repeats)
			repeats = max(1, repeats)
			
			#sys.stdout.write(str(repeats))
			#sys.stdout.flush()
			
			for i in range(repeats):
				fh.write(str(typeNow))
				fh.flush()

			typeNow = int(not typeNow)
			cntType = 1

		cnt+=1
		sys.stdout.write("\r" + str(cnt))
		sys.stdout.flush()
		#print "\r", cnt,
	fh.close()

#checkAllImages()
#print countCorrectFrameBy(1)
#for each bit, check expected and fix it if needed

def analyzeExpectedBits():
	global expBits, nowBit

	expBitsArr = []
	while nowBit < len(expBits):
		cb = expBits[nowBit] #expected bit
		nb = 0 #count expected bits
		while nowBit < len(expBits):
			if expBits[nowBit] == cb:
				nb     += 1
				nowBit += 1
			else:
				break
		expBitsArr += [[int(cb), nb]]
		#print [nb, cb]
	return expBitsArr

recoverFile()

#expBitsArr = analyzeExpectedBits()
#print expBitsArr[:4]
#print "Ignoring: ", str(countCorrectFrameFromCacheBy('1')) #ignore start
'''
totaladd = 0
totalrem = 0
for (b, n) in expBitsArr:

	cnt = countCorrectFrameFromCacheBy(str(b))
	#cnt = countCorrectFrameBy(int(b))
	#print "\r", "NOW:", now, "CNT:",cnt, "BIT:",b
	sys.stdout.flush()

	if 8*n - cnt > 3:
		#print "NOW:", now, "CNT:",cnt, "BIT:",b, "EXP:", n*8
		print 'Add Frames: ', (8*n-cnt), now
		addFrames(8*n-cnt)
		totaladd+= (8*n-cnt)
	elif 8*n - cnt < -3:
		#print "NOW:", now, "CNT:",cnt, "BIT:",b, "EXP:", n*8
		print 'Remove Frames: ', (8*n-cnt), now
		if(abs(8*n-cnt) < 50):
			removeFrames(abs(8*n-cnt))
		totalrem+= abs(8*n-cnt)

	#total += abs(8*n-cnt)
		#addFrames(8*n-cnt)
	#if now > 1000:
	#	break

print "TOTAL:", totaladd, (totalrem-6397)

#fh.close()'''