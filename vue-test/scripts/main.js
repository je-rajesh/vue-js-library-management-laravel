// for components 101.
Vue.component('tabs', {
    template: `
    <div>
        <div class="tabs">
            <ul>
                <li v-for="tab in tabs" :class="{'is-active' : tab.isActive }" @click="selectTab(tab)"> <a :href="tab.ohref"> {{ tab.name }} </a></li>
            </ul>
        </div>

        <div class="tabs-details">
            <slot></slot>
        </div>
    </div>`,

    created() {
        this.tabs = this.$children;
    },
    data() {
        return {
            tabs: [],
        }
    },

    methods: {
        selectTab(selectedTab) {
            this.tabs.forEach(tab => {
                tab.isActive = (tab.name === selectedTab.name);
            });
        }
    }


});

Vue.component('tab', {
    template: ` 
    <div v-show="isActive"><slot></slot></div>
`,
    props: {
        name: { required: true },
        selected: { default: false },
    },

    data() {
        return {
            isActive: false,
            href: '',
        };
    },

    computed: {
        ohref() {
            return '#' + this.name.toLowerCase().replace(/ /g, '-');
        }
    },

    mounted() {
        this.isActive = this.selected;
    },


});