#!/usr/bin/python

import sys
from random import randint, seed
'''
arm:

input 
hash(input) -> server

run: 
python monkey.py > demo.c && gcc demo.c -o demo && ./demo h4x0r1sh
copy the md5 hash from there, edit demo.c and add the new flag in sol variable
recompile demo.c & strip everything
solution is h4x0r1sh
'''

funcs = ["simple_enc", "tert_enc"]
noise_code = [
	[3, "int i=0,x=0,y=0;for(i=0;i<__1__;i++){if(i<__2__){i+=2;if(i%2==0)x+=15;else x-=10;}else if(i==__3__){i+=1;if(i%3==0)y+=13;else y-=10;}else i+=3;}"],
	[2, "int x=__1__; int y=__2__; int tmp; if(x>y) { tmp=x; x=y; y=tmp; } else { tmp=y; y=x/2; x=tmp*2;}"],
	[1, "int n=__1__, first = 0, second = 1, next, c; for ( c = 0 ; c < n ; c++ ) { if ( c <= 1 ) next = c; else { next = first + second; first = second; second = next; }}"],
]
funcs_reserved = []

def generate_random_noise(index):
	(n, noise) = noise_code[index]
	for i in range(n):
		seed()
		noise = noise.replace("__"+str(i+1)+"__",str(randint(1, 255)))

	return noise

def simple_enc(or_val, layer = 0, max_layers = 1):
	'''
	return orval [random_operator] [random_val]
	'''
	operators = ["*","-","+","&","|"]# /

	ret = dict()
	ret['calls'] = ''
	ret['definitions'] = ''
	layer = layer

	seed()
	operator = operators[randint(0, len(operators)-1)]
	seed()
	change_val = str(randint(0, 255))

	#make sure is the function name unique
	while True:
		if "simple_enc"+str(layer) in funcs_reserved:
			layer +=1 
			max_layers+=1
			continue

		funcs_reserved.append("simple_enc"+str(layer))
		break

	if layer == max_layers:
		ret['calls'] = change_val
	else:
		ret = simple_enc(or_val, layer+1, max_layers)


	ret['definitions'] += "unsigned char simple_enc"+str(layer)+"(unsigned char val) { "+generate_random_noise(randint(0,len(noise_code)-1))+" return (val "+operator+" "+ret['calls']+"); }\n"
	if layer == 0:
		#pass
		ret['calls'] = 'simple_enc'+str(layer)+"("+str(or_val)+")"
	else:
		ret['calls'] = 'simple_enc'+str(layer)+"(" + change_val + ")"
	
	return ret

def tert_enc(or_val, layer = 0, max_layers = 1):
	'''
	return static_val [random_operator] [random_val] ? [tert_enc]: [simple_enc]
	'''
	operators = [">","<",">=","<=","==","!="]

	ret = dict()
	ret2 = dict()
	ret['calls'] = ''
	ret['definitions'] = ''
	ret2['calls'] = ''
	ret2['definitions'] = ''
	layer = layer

	seed()
	operator = operators[randint(0, len(operators)-1)]
	seed()
	change_val = str(randint(0, 255))
	change_val2 = str(randint(0, 255))

	#make sure is the function name unique
	while True:
		if "tert_enc"+str(layer) in funcs_reserved:
			layer +=1 
			max_layers+=1
			continue

		funcs_reserved.append("tert_enc"+str(layer))
		break

	if layer == max_layers:
		ret['calls'] = change_val
		ret2['calls'] = change_val2
	else:
		ret = tert_enc(or_val, layer+1, max_layers)
		ret2 = simple_enc(or_val, layer, max_layers)

	ret['definitions'] += ret2['definitions']
	ret['definitions'] += "unsigned char tert_enc"+str(layer)+"(unsigned char val) { "+generate_random_noise(randint(0,len(noise_code)-1))+" return (val "+operator+" "+change_val+" ? "+ret['calls']+": "+ret2['calls']+"); }\n"
	if layer == 0:
		ret['calls'] = 'tert_enc'+str(layer)+"("+str(or_val)+")"
	else:
		ret['calls'] = 'tert_enc'+str(layer)+ "(" + change_val + ")"
	
	return ret

def mixn(inp=0, n=3, layer=0, max_layers=1):
	operators = ["*","-","+","&","|","^"]

	ret = dict()
	ret['calls'] = ''
	ret['definitions'] = ''
	definition = []

	#make sure is the function name unique
	while True:
		if "mixn"+str(layer) in funcs_reserved:
			layer +=1 
			max_layers+=1
			continue

		funcs_reserved.append("mixn"+str(layer))
		break

	for i in range(n):
		seed()
		operator   = operators[randint(0, len(operators)-1)]
		seed()
		change_val = str(randint(0, 255))
		seed()

		change_val = globals()[funcs[randint(0, len(funcs)-1)]](change_val, layer+i, max_layers+i)
		ret['definitions'] += change_val['definitions']
		if i:
			definition.append(operator + " " +change_val['calls']+" ")
		else:
			definition.append(change_val['calls']+" ")

	ret['definitions'] += "unsigned char mixn"+str(layer)+"(unsigned char inp) { "+generate_random_noise(randint(0,len(noise_code)-1))+" return " + "".join(definition) + " + inp; } "
	ret['calls'] = 'mixn'+str(layer)+"("+str(inp)+")"
	return ret


def generate_n_chars(varname = 'inp', n=7):
	ret = {'definitions': '', 'calls': ''}

	for i in range(n):
		mn = mixn(inp=varname + "["+str(i)+"]", n=randint(5,15), max_layers=randint(5,12))
		ret['definitions'] += mn['definitions']
		ret['calls'] += varname + "["+str(i)+"]=" + mn['calls'] + ";"

	return ret

x= generate_n_chars(varname="msg", n=8)

fh = open("chall_template.c","r")
template = fh.read()
fh.close()
template = template.replace("##definitions##", x['definitions'])
template = template.replace("##calls##",x['calls'])
print template

