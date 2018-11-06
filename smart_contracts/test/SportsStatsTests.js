const SportsStat = artifacts.require('SportsStat');

contract('SportsStat', accounts => {
    var defaultAccount = accounts[0];
    var user1 = accounts[1];

    let contract;

    beforeEach(async () => {
        // Create the contract
        contract = await SportsStat.new({
            from: defaultAccount
        });
    });

    describe('Create a stat', () => {
        it('Creates a stat', async () => {
            await contract.createRunStat('A', 'B', 'C', 'D', 'E', 'F', {
                from: user1
            });
            const runStats = await contract.getRunStats({
                from: user1
            });
            assert.equal(runStats, '%r%A%s%B%s%C%s%D%s%E%s%F');
        });
        it('Creates mutliple stats', async () => {
            await contract.createRunStat('A', 'B', 'C', 'D', 'E', 'F', {
                from: user1
            });
            await contract.createRunStat('A', 'B', 'C', 'D', 'E', 'F', {
                from: user1
            });
            const runStats = await contract.getRunStats({
                from: user1
            });
            assert.equal(runStats, '%r%A%s%B%s%C%s%D%s%E%s%F%r%A%s%B%s%C%s%D%s%E%s%F');
        });
    });
    describe('Creates stats user specific', () => {
        it('works', async () => {
            await contract.createRunStat('default', 'account', 'C', 'D', 'E', 'F', {
                from: defaultAccount
            });
            await contract.createRunStat('user', 'one', 'C', 'D', 'E', 'F', {
                from: user1
            });
            const runStats = await contract.getRunStats();
            assert.equal(runStats, '%r%default%s%account%s%C%s%D%s%E%s%F');
        });
        it('can access different users', async () => {
            await contract.createRunStat('default', 'account', 'C', 'D', 'E', 'F', {
                from: defaultAccount
            });
            await contract.createRunStat('user', 'one', 'C', 'D', 'E', 'F', {
                from: user1
            });
            const runStats = await contract.getRunStats({
                from: user1
            });
            assert.equal(runStats, '%r%user%s%one%s%C%s%D%s%E%s%F');
        });
    });
});
