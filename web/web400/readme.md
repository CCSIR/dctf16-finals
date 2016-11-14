#Web 400 - Anomaly WAF
Our security specialist developed a new IPS system with state of the art technology. He told us that his IPS is bullet proof and Web Attacks will not pass. He is so confident of this IPS that he made a SQLI vulnerability in the following link and told us that if we can get the first row from table `flag`, he will send us a bottle of whisky and he'll get back to support desk. Can you help us get drunk? :-)

https://web400.dctf-finals16.def.camp/

#Solution
The main idea is that anomaly based algorithms which are into "self-learning" mode can be learned malicious rules by feeding with small changes from the accepted versions until they believe that a malicious activity is legitimate. A solution example can be found in sol.py