import data from "./var";

function login(cy) {
  cy.visit("/");
  cy.get("#user > input").type(data.email);
  cy.get("#password > input").type(data.password);
  cy.get("#login").click();
}

export default {
  login
};
