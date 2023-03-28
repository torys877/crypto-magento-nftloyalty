/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

import { ethers } from "hardhat";

async function main() {
  const ContractFactory = await ethers.getContractFactory("NFTLoyaltyM2");

  const instance = await ContractFactory.deploy();
  await instance.deployed();

  console.log(`Contract deployed to ${instance.address}`);
}

// We recommend this pattern to be able to use async/await everywhere
// and properly handle errors.
main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
