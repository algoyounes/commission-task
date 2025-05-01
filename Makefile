#
# Commission Calculator Makefile
#

setup:
	@echo "$(CLR_BLUE)Setting up environment...$(CLR_END)"
	(docker compose run --rm php fish)
