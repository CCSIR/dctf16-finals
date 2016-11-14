/* todo a file for flag, edit path && socat 

92233720360000000000 SOLUTION
Inspired from 2014/picoctf challenge.
*/

#include <stdio.h>
#include <stdint.h>
#include <stdlib.h>
#include <time.h>
#include <unistd.h>
#include <limits.h>

long boss_cash = 99999999999;
long player_cash = 0;

const char *wins[10] = {
    "Nice!",
    "Not bad!",
    "You won!", 
    "Ya earned it.",
    "I'll revenge.",
    "Damn, not again.",
};

const char *loses[10] = {
    "Oops.",
    "Pam pam pam.",
    "You looooose!",
    "Loser!",
    "Maybe next time.",
    "Who hohoho. :D",
};

int is_digit(char c) {
    return '0' <= c && c <= '9';
}

long getnum() {
    printf("> "); fflush(stdout);
    uint64_t num = 0;

    char c = 0;
    while(!is_digit(c)) {
        c = getchar();
        if(c == EOF) {
            puts("Oouuups!");
            exit(0);
        }
    }

    while(is_digit(c)) {
        if(num >= LONG_MAX) {
            num = LONG_MAX;
            break;
        }
        num *= 10;
        num += c - '0';
        c = getchar();
        if(c == EOF) {
            puts("Oouuups!");
            exit(0);
        }
    }

    while(c != '\n') {
        c = getchar();
        if(c == EOF) {
            puts("Oouuups!");
            exit(0);
        }
    }

    return num;
}

long getbet() {
    while(1) {
        printf("You have $%lu. How much you'll bet this round?\n", player_cash);
        long bet = getnum(); 
        if(bet <= player_cash) {
            return bet;
        } else {
            puts("Too much for you! Earn some more money or play on other table.");
        }
    }
}

long getchoice(long bet) {
        printf("1: < 10 Win your bet back plus an additional $%lu if you have a shortie.\n", 35*bet);
        printf("2: 10-30 Win your bet back plus an additional $%lu if you have some feelings.\n", 10*bet);
        printf("3: 30-50 Win your bet back plus an additional $%lu if you're cool .\n", 5*bet);
        printf("4: 50-80 Win your bet back plus an additional $%lu if you're so amazing.\n", 5*bet);
        printf("5: 80-90 Win your bet back plus an additional $%lu on this call.\n", 10*bet);
        printf("6: > 90 Win your bet back plus an additional $%lu on evel powers.\n", 35*bet);
        while(1) {
            puts("Any plans?");
            long choice = getnum();
            if(1 <= choice && choice <= 6) {
                return choice;
            } else {
                puts("Wrong choice, dude!");
            }
        }
}

void grantFlag() {
    char buffer[100];
    FILE *f = fopen("/flag.txt", "r");
    if(!f) {
        puts("Some files error with the flag path.");
    } else {
        size_t count = fread(buffer, 1, sizeof(buffer), f);
        fclose(f);
        fwrite(buffer, 1, count, stdout);
    }
}

int rand_nr() {
    return (rand()%100)+1;
}

int roll() {
    int i;
    printf("%d", rand_nr());
    for(i = 0; i < 50; i++) {
        fflush(stdout);
        usleep(10000);
        printf("\r%d", rand_nr());
    }
    int ret = rand_nr();
    printf("\r%d\n", ret);
    fflush(stdout);
    return ret;
}

void play(long choice, long bet, int generated) {
    if(choice < 0 || choice > 100) {
        printf("I don't like your choice.\n");
        exit(0);
    }
    if(generated < 10) {
        if(choice < 10) {
            player_cash += 35*bet;
            boss_cash -= 35*bet;
            puts(wins[rand()%3+0]);
        } else {
            puts(loses[rand()%3+0]);
        }
    } else if(generated >= 10 && generated <= 30) {
        if(choice >= 10 && choice <= 30) {
            player_cash += 10*bet;
            boss_cash -= 10*bet;
            puts(wins[rand()%3+1]);

        } else {
            puts(loses[rand()%3+1]);
        }
    } else if(generated > 30 && generated <= 50) {
        if(choice > 30 && choice <= 50) {
            player_cash += 5*bet;
            boss_cash -= 5*bet;
            puts(wins[rand()%3+2]);

        } else {
            puts(loses[rand()%3+2]);
        }
    } else if(generated > 50 && generated <= 80) {
        if(choice > 50 && choice <= 80) {
            player_cash += 5*bet;
            boss_cash -= 5*bet;
            puts(wins[rand()%3+3]);

        } else {
            puts(loses[rand()%3+3]);
        }
    } else if(generated > 80 && generated <= 90) {
        if(choice > 80 && choice <= 90) {
            player_cash += 10*bet;
            boss_cash -= 10*bet;
            puts(wins[rand()%3+1]);

        } else {
            puts(loses[rand()%3+1]);
        }
    } else if(generated > 90) {
        if(choice > 90) {
            player_cash += 35*bet;
            boss_cash -= 35*bet;
            puts(wins[rand()%3+0]);

        } else {
            puts(loses[rand()%3+0]);
        }
    }
    

   
}

void seedrand() {
    FILE *f = fopen("/dev/urandom", "r");
    unsigned seed;
    fread(&seed, sizeof(seed), 1, f);
    srand(seed);
    fclose(f);
}

int main(int argc, char *argv[]) {
    seedrand();
    setlinebuf(stdout);
    setlinebuf(stdin);

    puts("Ready for a play?");

    switch(rand()%10) {
        case 0:
        case 1:
        case 2:
        case 3:
            puts("Some warmup, just for you.");
            boss_cash -= 3; player_cash += 3;
            break;
        case 4:
        case 5:
        case 6:
        case 7:
            puts("Take some steroids and let's play!");
            boss_cash -= 30; player_cash += 30;
            break;
        case 8:
        case 9:
            puts("Insane money! Ready to loose them all?");
            boss_cash -= 50; player_cash += 50;
            break;
    }

    long bet;
    long choice;
    while(player_cash > 0) {
        bet = getbet();
        player_cash -= bet;
        boss_cash += bet;
        choice = getchoice(bet);

        seedrand();

        puts("Let's see how lucky you are!");
        int x = roll();
        play(choice, bet, x);

        if(boss_cash < 0) {
            puts("I lost all the money. Go away with this flag");
            grantFlag();
            exit(0);
        }
    }
    puts("Huston, you have a problem. Come up with cash or go away!");
    return 0;
}