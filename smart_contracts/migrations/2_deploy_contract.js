var SportsStat = artifacts.require("../contracts/SportsStat.sol");

module.exports = (deployer) => {
    deployer.deploy(SportsStat);
};
