/* Author: Kelvin Ng <kelvin9302104 at gmail dot com>
 * License: GPL
 * Usage: ./setuid_run_cmd <uid> <command>
*/

#include <unistd.h>
#include <cstdio>
#include <cstdlib>
#include <sstream>

using std::stringstream;

int main(int argc, char ** argv)
{
	if (setuid(atoi(argv[1])) == 0)
	{
		stringstream ss;
		for (int i = 2; i < argc; ++i)
			ss << argv[i] << " ";

		system(ss.str().c_str());
	}	
	else
		printf("Error setting uid\n");
}

