from glob import iglob
import shutil
import os

PATH = r'./images/'

destination = open('all.png', 'wb')

for i in range(0,1217):
	print PATH + str(i) + ".png"
	shutil.copyfileobj(open(PATH + str(i) + ".png", 'rb'), destination)
destination.close()