import Vue from "vue";

export interface Answers {
  failure: number;
  success: number;
  list: boolean[];
}

export interface Card {
  question: string;
  answer: string;
  answers: Answers;
}

export interface Deck {
  name: string;
  description: string;
  cards: Card[];
}

declare module "vue/types/vue" {
  interface Vue {
    $Notify: any;
  }
}
