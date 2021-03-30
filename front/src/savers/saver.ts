export default abstract class Saver {
  abstract async getCurrentUser();

  abstract async register(credentials);

  abstract async login(credentials);

  abstract async logout();

  abstract async getPublicDecks();

  abstract async getMyDecks();

  abstract async getFavoriteDecks();

  abstract async getDeck(deckid);

  abstract async createDeck(deck);

  abstract async editDeck(data);

  abstract async publishDeck(deck);

  abstract async addToFavorites(deck);

  abstract async removeFromFavorites(deck);

  abstract async createCard(data);

  abstract async editCard(data);

  abstract async setAnswer(data);
}
