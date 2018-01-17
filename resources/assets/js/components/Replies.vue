<template>
   <div>
       <div v-for="(reply, index) in items" :key="reply.id">
           <reply :data = "reply" @deleted="remove(index)"></reply>
       </div>

       <paginator :dataSet="dataSet" @updated="fetch" ></paginator>

       <new-reply @created="add"></new-reply>

   </div>
</template>
<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collection from '../mixins/Collection';

    export default {
        mixins: [collection],
        data() {
            return { dataSet : false };
        },
        created()
        {
            this.fetch();
        },
        components: {
            'reply' : Reply,
            'new-reply': NewReply
        },

        methods : {
            fetch(page)
            {
                axios.get(this.url(page)).then(this.refresh)
            },
            url(page)
            {
                if(!page)
                {
                   let query = location.search.match(/page=(\d+)/);

                   page = query ? query[1] :  1;
                }

                return `${location.pathname}/replies?page=${page}`;
            },
            refresh({data})
            {
                 this.dataSet = data;

                 this.items = data.data;

                 window.scrollTo(0, 0);
            }
        }
    }

</script>
