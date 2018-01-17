<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue'

    export default {
       props: ['initialRepliesCount'],
       data()
       {
           return{
               repliesCount: this.initialRepliesCount,
           }
       },
        components: {
           'replies' : Replies,
            'subscribe-button': SubscribeButton,
        }
    }
</script>