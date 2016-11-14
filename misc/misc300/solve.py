import PIL.Image
import imageio, numpy,re,sys

reader = imageio.get_reader('surveillance.mp4')
fps = reader.get_meta_data()['fps']

def crop(image, startx, stopx, starty, stopy):
	image = numpy.array(image)
	return image[startx:stopx, starty:stopy]

def recoverFile():
	fh  = open('recovered.txt', 'w')
	cnt = 0
	typeNow = 1
	cntType = 0
	for im in reader:
		#print ".",
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

recoverFile()

