/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

import { expect } from "chai";
import { ethers } from "hardhat";

describe("NFTLoyaltyM2", function () {
  it("Test contract", async function () {
    const ContractFactory = await ethers.getContractFactory("NFTLoyaltyM2");

    const instance = await ContractFactory.deploy();
    await instance.deployed();

    expect(await instance.name()).to.equal("NFTLoyaltyM2");
  });
});
