# Misc 250: Victim's Investigation

A blackhat hacker was raided last night. Forensic analyst were unable to inspect his data. The only evidence we might have are the traces that he left on the machine that he hacked - we cloned it for you. Can you tell us if there's anything malicious?

https://storage.dctf-finals16.def.camp/victim.gz

# How to solve:
* download victim.gz which contains a container
* bash_history contains a file which was downloaded by the intruder, compared with a hash and then removed
* flipped.c contains the file discovered in the container "hidden" in a folder called ".. "
* flipped_explained.c contains all the explanations regarding flipped bits, most of them were easy to manually fix but the flag contained two other bits which weren't so obvious (b to f & e to a); you could easily brute force using https://github.com/conorpp/bitflipper (changing the algorithm from sha1 to md5)