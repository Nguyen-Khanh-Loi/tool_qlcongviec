export const myModule = {
    namespaced: true,
    state() {
        return {
            username: 'demo',
            age: 1,
        }
    },
    // state: {
    //     safelyStoredNumber: 0
    // },
    getters: {
        getUser(state) {
            console.log(state);
            return state.username;
        },
        getAge(state) {
            // if (state.age == 2) {
            //     console.log($router);
            //     //this.$router.push('/dashboard');
            // }
            return state.age;
        }
        // safelyStoredNumber: state => state.safelyStoredNumber,
        // storedNumberMatches(state) {
        //     return matchNumber => {
        //         return state.safelyStoredNumber === matchNumber;
        //     }
        // }
        // Cách ngắn gọn hơn:
        // storedNumberMatches: state => matchNumber => state.safelyStoredNumbers === matchNumber
    },
    mutations: {
        // changeUsername(state, newUsername) {
        //     state.username = newUsername;
        // },
        changeAge(state, newAge) {
            state.age = newAge;
        }
    },
    actions: {
        handleChangeUsername(context, newUsername) {
            // if (newUsername) {
            //     router.push({name: 'dashboard'});
            // }
            //
            context.commit('changeUsername', newUsername);
        },
        // async setNumberToRemoteValue(context, age) {
        //     context.commit('setStoredNumber', await myRemoteService.getRemoteValue());
        //     return Promise.resolve();
        // },
        handleChangeAge(context, newAge) {
            console.log(newAge);
            if (newAge) {
                router.push({ name: 'login' });
            }
            // router.push({name: 'login'});
            context.commit('changeAge', newAge);
        },
    }
    // mutations: {
    //     incrementStoredNumber(state) {
    //         state.safelyStoredNumber++;
    //     },
    //     setStoredNumber(state, newNumber) {
    //         state.safelyStoredNumber = newNumber;
    //     }
    // },
    // actions: {
    //     async setNumberToRemoteValue(context) {
    //         context.commit('setStoredNumber', await myRemoteService.getRemoteValue());
    //         return Promise.resolve();
    //     },
    // }
}
