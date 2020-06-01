import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  cellContainerStyle: {
    backgroundColor: Colors.background.login,
    paddingHorizontal: Metrics.baseMargin,
    flexDirection: "row",
    flexWrap: "wrap"
  },
  cellStyle: {
    width: Metrics.graphCellWidth,
    borderColor: Colors.background.login,
    borderTopWidth:Metrics.ratio(1),
    alignItems:"center",
    justifyContent:"center",
    backgroundColor: Colors.background.primary,
    paddingVertical:Metrics.doubleBaseMargin
  },
  imageStyle: {
    alignSelf: "flex-end"
  },verticalSeparator:{
    position:"absolute",height:Metrics.ratio(40),backgroundColor:Colors.background.login,width:Metrics.ratio(1),right:0
  },
  descriptionWrapper:{flexDirection: "row",alignItems:"center"},
  container:{alignItems:"center",justifyContent:"center"},
  text:{
    paddingTop:Metrics.smallMargin
  }
});
