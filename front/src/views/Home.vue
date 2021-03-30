<template>
  <div class="home">
    <h1>Home</h1>
    <p>Sapio Cards is an online tool that allows you to learn efficiently.</p>
    <p>Just create cards with a question in the front and an answer in the back.</p>
    <p>
      To know more about flashcards you can watch the video of Thomas Frank:
      <br />
      <a href="https://www.youtube.com/watch?v=mzCEJVtED0U&ab_channel=ThomasFrank"
        target="_blank"
      >
        https://www.youtube.com/watch?v=mzCEJVtED0U&ab_channel=ThomasFrank
      </a>
    </p>
    <p>The application is smart and can show you the cards you fail often.</p>
    <p>Follow those steps:</p>
    <p>
      <ol>
        <li>Create an account</li>
        <li>Create a deck, a deck is a collection of cards</li>
        <li>Create cards within the deck with a question and an answer</li>
        <li>Click play and click on the Yes or No button depending on your knowledge</li>
      </ol>
    </p>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from "vue-property-decorator";

@Component({})
export default class Home extends Vue {
  mounted() {
    console.log("mounted");
  }
}
</script>

<style lang="scss" scoped>
p,
ol {
  margin-bottom: 10px;
}

ol {
  padding-left: 20px;
}

ol li {
  list-style-type: decimal;
}
</style>
