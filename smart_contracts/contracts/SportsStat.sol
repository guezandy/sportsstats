pragma solidity ^0.4.23;

import "openzeppelin-solidity/contracts/ownership/Ownable.sol";

contract SportsStat is Ownable {
    // TODO: Break into more models
    struct RunStat {
        string eventName;
        string timestamp;
        string place;
        string distance;
        string time;
        string description;
    }

    // Need to make this not public
    mapping(address => RunStat[]) public addressToStats;
    string public statDelim = "%s%";
    string public runDelim = "%r%";

    function createRunStat(string eventName, string timestamp, string place, string distance, string time, string description) public {
        // TODO: Not let this be called?
        addressToStats[msg.sender].push(RunStat(eventName, timestamp, place, distance, time, description));
    }

    function getRunStats() public view returns (string) {
        string memory encodedRunStats;
//        RunStat[] storage runStats = addressToStats[msg.sender];
        for (uint i=0; i<addressToStats[msg.sender].length; i++) {
            RunStat storage stat = addressToStats[msg.sender][i];
            encodedRunStats = string(abi.encodePacked(encodedRunStats, runDelim, stat.eventName, statDelim, stat.timestamp, statDelim, stat.place, statDelim, stat.distance, statDelim, stat.time, statDelim, stat.description));
        }
        return encodedRunStats;
    }
}
