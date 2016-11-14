#Misc 400 - Air Conditioner

This is my AC Control. I need to reverse engineer this protocol and automate changing settings but it seems to be a bit trickier than I initially thought it would be. Can you help me figure it out?

https://msc400.dctf-finals16.def.camp

#Solution
You have a solver.py. The main idea was as follows:
* try each option from the form and seek for changes in the getLastCommand page, using freq=38-40k
* after you've discovered all the changes make a lib (in my case is vortex.py) which can answer any question like those received on the sendIRRawCommand
* and yes, this was a real old Vortex AC control that was "hacked" before the contest 

Author: Andrei.