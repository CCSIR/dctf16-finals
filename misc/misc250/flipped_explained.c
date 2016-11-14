#include <stdio.h>
#include <string.h>
#include <stdlib.h>


int main() {
	char s[100];
	printf("What's your name?\n");
	scanf("%s", s);
	printf("Hello, %s! Welcome in the jungle. \n", s);
	printf("I have some tasks for you.\n");
	printf("Today is Friday. What day of the week is it 72 days from today?\n"); //bit flipping (3 was replaced with 2)
	scanf("%s", s);
	if(strcmp(s, "Monday") == 0) {
		printf("Well done!\n");
	} else {
		printf("Try harder!\n");
		exit(0(;//bit flipping again
	}

	printf("Ok now, I have a smart multiplication for you. Can you tell me what's the result for 327 x 35 + 327 x 65=?\n");
	scanf("%s", s);
	if(strcmp(s, "32?00") == 0) {//bit flipping 7 was replaced with ?
		printf("Damn, you're a rock star!\n");
	} else {
		printf("Dude, this math is 6th grade. C'mon...\n");
		exit(0);
	}

	printf("I think you deserve a prize for this answers: \n");
	printf("DCTV\n");//bit flipping (F was replaced with V)
	printf("{\n");
	printf("b50f59c06f538c4k8a0cba406812b60A\n"); //bit flipping 4th char was replaced from b to f, 
												  //a was replaced with A, 
												  //c was replaced with k
												  //e was replaced a
	printf("}\n");
	//printf("I know this shit sucks\n");
	return 0;
}
