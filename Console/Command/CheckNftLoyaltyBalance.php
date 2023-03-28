<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace Crypto\NftLoyalty\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Crypto\NftLoyalty\Helper\Config;

class CheckNftLoyaltyBalance extends Command
{
    public const CLIENT_ADDRESS = 'client_address';
    public const NFT_ADDRESS = 'nft_address';

    private Config $config;

    public function __construct(
        Config $config,
        string                     $name = null
    ) {
        parent::__construct($name);

        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setName('crypto:nft:balance');
        $this->setDescription('Check Nft Balance');
        $this->addOption(
            self::CLIENT_ADDRESS,
            null,
            InputOption::VALUE_REQUIRED,
            'Client Address'
        );

        $this->addOption(
            self::NFT_ADDRESS,
            null,
            InputOption::VALUE_REQUIRED,
            'NFT Address'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $clientAddress = (string) $input->getOption(self::CLIENT_ADDRESS);
        $nftAddress = (string) $input->getOption(self::NFT_ADDRESS);

        if (!$clientAddress) {
            $output->writeln("<error>Please specify an client address in network</error>");
            return Cli::RETURN_FAILURE;
        }

        if (!$nftAddress) {
            $output->writeln("<error>Please specify an NFT address in network</error>");
            return Cli::RETURN_FAILURE;
        }

        $resultData = $this->config->getNftBalance($clientAddress, $nftAddress);

        $err = $resultData['error'];
        $result = $resultData['result'];

        if ($err) {
            print_r('ERROR => ' . $err . PHP_EOL);
        }

        print_r('Balance => ' . $result . PHP_EOL);

        return Cli::RETURN_SUCCESS;
    }
}
