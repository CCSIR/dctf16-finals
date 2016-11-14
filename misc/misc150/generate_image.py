#!/usr/bin/env python

import scipy.misc
import random
import numpy as np


import PIL
from PIL import ImageFont
from PIL import Image
from PIL import ImageDraw

width = 100
height = 100

def drawImage(text, save_to, width = 100, height = 100):
	img = Image.new( 'RGB', (width,height), "black") 
	pixels = img.load()

	for i in range(img.size[0]):    # for every pixel:
		random.seed()
		for j in range(img.size[1]):
			pixels[i,j] = (random.randrange(0,255), random.randrange(0,255), 1) # set the colour accordingly

	font = ImageFont.truetype("Lato-Bold.ttf",30)
	draw = ImageDraw.Draw(img)
	draw.text((random.randrange(0, 70), random.randrange(0,70)),text,(0,0,0),font=font)
	draw = ImageDraw.Draw(img)

	#img.show()
	img.save(save_to)

#drawImage("DCTF", "DCTF.png", width, height)

stringToDraw="S05TV0c1TFNORjJIU0lESk9NUUhJMkRGRUJTR0taM1NNVlNTQTMzR0VCWkdLNDNKT04yR0MzVERNVVFISTNaTUVCWFhFSURRT0pYWElaTERPUlVXNjNSQU1aWkc2M0pNRUJVR0M0VE5GWVFFUzVCQU1GWUhBM0RKTVZaU0E1RFBFQlFXNDZKQU9aMldZM1RGT0pRV0UzREZFQlFXNFpCQU9aUVdZNUxCTUpXR0tJREJPTlpXSzVCTUVCWlhLWTNJRUJRWEdJREJFQllHSzRUVE41WENZSURFTzVTV1kzREpOWlRTWUlERE41V1cyNUxPTkYySFNMQkFORjJHSzNKTUVCWEdDNURKTjVYQ1lJRFBPSVFHNjRUSE1GWEdTNlRCT1JVVzYzUk9CSkFYR0lET041MkdLWkJBTUo0U0E1RElNVVFFUzNUVE9SVVhJNUxVTVVRR00zM1NFQkpXS1kzVk9KVVhJNkpBTUZYR0lJQ1BPQlNXNElDTk1WMkdRMzNFTjVXRzZaM0pNVlpTQUtDSktOQ1VHVDJORkVRR1MzUkFPUlVHS0lDUEtOSlZJVEtORUFaU1lJRFRNVlJYSzRUSk9SNFNBNERTTjUzR1NaREZPTVFDRVlKQU1aWFhFM0pBTjVUQ0E0RFNONTJHS1kzVU5GWFc0SURYTkJTWEVaSkFNRVFIR1pMUU1GWkdDNURKTjVYQ0EyTFRFQlJYRVpMQk9SU1dJSURDTVYySE9aTEZOWVFISTJERkVCUVhHNDNGT1JaU0FZTE9NUVFISTJERkVCMkdRNFRGTUYyQzRJUkFLUlVHSzQzRkVCWldLNERCT0pRWEkyTFBOWlpTQVlMU01VUUdPWkxPTVZaR1NZM0JOUldIU0lERE1GV0dZWkxFRUFSR0czM09PUlpHNjNEVEZRUkNBWUxPTVFRSEczM05NVjJHUzNMRk9NUUdTM1RETlIyV0laSkFNTlVHQzNUSE1WWlNBNURQRUIyR1FaSkFNRlpYR1pMVUVCWFhFSURVTkJTU0E1RElPSlNXQzVCT0VCTFdRWkxPRUJaV0tZM1ZPSlVYSTZKQU5WU1dLNURURUJYV0U0M0RPVlpHUzVEWkVCUVhJSUNFSU5LRU02M0dHUlJEUU9CU0dBM1dDTUJVTVZRV0NaQlhNUTRXS05KVEdZWkdDTlpVTVk0RE9OVEZHRjZTQU1SUUdFM0E9PT09"
for i in range(len(stringToDraw)):
	drawImage(stringToDraw[i], "images/" + str(i+1) + ".png", width, height)