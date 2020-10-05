import { StyleSheet,Dimensions } from "react-native";
import { Colors, Metrics } from "../../theme";
import { color } from "react-native-reanimated";
var {width, height} = Dimensions.get ('window');

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary,
    //  marginHorizontal:12
  },
  signText:{
    fontSize:15,
    textAlign:"center",
    color:Colors.text.primary,
    padding:width*0.065
},
signTextTwo:{
    fontSize:26,
    color:Colors.status.accepted,
    textAlign:"center",
    // marginTop:5

},
buttonSignIn:{
  // bottom:5,
  // position: 'absolute',
  // justifyContent: 'center',
  // alignItems: 'center',
  // position: 'absolute', //Here is the trick
  bottom: 70,
  justifyContent: 'flex-end',
  flex: 1,
  // marginHorizontal:12
},
thankyouImgStyle:{
  width:50,
  height:50.
},

button: {
  //  borderWidth: 1,
    marginTop: height * 0.01,
    marginHorizontal: width * 0.01,
    height: height * 0.06,
  //  fontSize: width * 0.0
 },


 disableButton: {
  backgroundColor: '#a5a5a5',
  alignItems: 'center',
  justifyContent: 'center',
  // marginVertical: height * 0.01,
  padding: height * 0.017,
},

disableTitleButton: {
  color: 'white',
  // fontFamily: Font.bold, 
  fontSize: width * 0.05,
  fontWeight: '700'
},

});