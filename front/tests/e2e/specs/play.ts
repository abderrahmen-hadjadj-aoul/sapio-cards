import utils from "../utils";

describe("Play", () => {
  it("should display play view and display correct stats", () => {
    utils.login(cy);
    // Create deck
    const name = "some name " + Math.random();
    const description = "some description " + Math.random();
    cy.get("#create-new-deck-button").should("exist");
    cy.get("#create-new-deck-button").click();
    cy.get("#name").type(name);
    cy.get("#description").type(description);
    cy.get("#create-button").click();
    // Create cards
    const MAX = 5;
    const responses = {};
    for (let i = 1, len = MAX; i <= len; i++) {
      cy.get("#create-card-button").should("not.have.attr", "disabled");
      const question = "some question " + i;
      const answer = "some answer " + i;
      responses[i] = {
        yes: 0,
        no: 0,
        answers: []
      };
      cy.get("#question").type(question);
      cy.get("#answer").type(answer);
      cy.get("#create-card-button").click();
      cy.get("#create-card-button").should("have.attr", "disabled");
    }
    cy.get("#create-card-button").should("not.have.attr", "disabled");
    // Check
    const checkOne = (i: number) => {
      const response = responses[i];
      const total = response.yes + response.no;
      let percent = "?";
      percent = "" + Math.ceil((100 * response.no) / total);
      const text = response.no + " / " + total + " - " + percent + "%";
      cy.get("td")
        .contains("some question " + i)
        .parent("tr")
        .find(".failures")
        .contains(text)
        .should("exist");
      cy.get("td")
        .contains("some question " + i)
        .parent("tr")
        .find(".last span.dot")
        .each((value, index) => {
          const green = response.answers[index];
          const not = green ? "" : "not.";
          cy.wrap(value).should(not + "have.class", "green");
        });
    };
    const check = () => {
      for (let i = 1, len = MAX; i < len; i++) {
        checkOne(i);
      }
    };
    // Play
    cy.get("#play-card-button").click();
    cy.get("#yes-button").should("not.have.attr", "disabled");
    let count = 0;
    const countMAX = 10;
    const respond = () => {
      if (count >= countMAX) {
        cy.get("#back").click();
        check();
        return;
      }
      cy.get("#yes-button").should("not.have.attr", "disabled");
      cy.get("#no-button").should("not.have.attr", "disabled");
      cy.get("#question")
        .invoke("text")
        .then(text => {
          console.log("text", text);
          const numberString = text.replace("some question ", "");
          const number = Number(numberString);
          console.log("number", number);
          const response = number > 2;
          const id = response ? "#yes-button" : "#no-button";
          const type = response ? "yes" : "no";
          responses[number][type]++;
          responses[number].answers.push(response);
          cy.get(id).click();
          count++;
          respond();
        });
    };
    respond();
  });
});
