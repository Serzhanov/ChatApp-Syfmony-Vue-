
import { createStore } from 'vuex'


import conversation from "./modules/conversation";
import user from "./modules/user";

export const store=createStore({
    modules: {
        conversation,
        user
    }
});