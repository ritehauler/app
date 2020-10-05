// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors, Fonts } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.primary,
    padding: Metrics.baseMargin,
    paddingLeft: Metrics.smallMargin * 1.5,
    marginTop: Metrics.smallMargin * 1.2
  },
  emptyView: {
    alignItems: "center",
    justifyContent: "center",
    marginVertical: Metrics.baseMargin
  },
  info: {
    marginTop: Metrics.baseMargin,
    lineHeight: Metrics.baseMargin * 1.25,
    marginHorizontal: Metrics.smallMargin / 2
  },
  volume: {
    marginTop: Metrics.smallMargin * 0.75,
    fontFamily: Fonts.type.medium,
    color: Colors.text.quaternary,
    fontSize: Fonts.size.xxxSmall
  },
  volumeAmount: {
    fontFamily: Fonts.type.regular,
    color: Colors.text.primary,
    fontSize: Fonts.size.xxxSmall
  },
  cartonImage: {
    flex: 1,
    alignItems: "center"
  },
  loadingView: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: Metrics.baseMargin * 4
  },
  image: {
    width: 200,
    height: 200
  }
});
