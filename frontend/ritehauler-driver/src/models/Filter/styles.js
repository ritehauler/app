// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  modal: {
    margin: 0
  },
  body: {
    flex: 1,
    backgroundColor: Colors.background.secondary
  },
  content: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  buttonContainer: { backgroundColor: Colors.background.primary },
  navBar: {
    flexDirection: "row",
    backgroundColor: Colors.background.secondary,
    alignItems: "center"
  },
  navTitle: {
    flex: 1,
    marginLeft: Metrics.baseMargin
  },
  separator: {
    height: 2
  },
  sortByDateContainer: {
    backgroundColor: Colors.background.secondary,
    padding: Metrics.baseMargin,
    margin: Metrics.baseMargin
  },
  sortByDate: {
    marginBottom: Metrics.smallMargin * 1.5
  },
  datesContainer: {
    flexDirection: "row"
  },
  endDate: {
    marginLeft: Metrics.smallMargin
  },
  cross: {
    padding: Metrics.baseMargin
  },
  clear: {
    padding: Metrics.baseMargin
  },
  filterOptions: {
    width: Metrics.screenWidth
  }
});
