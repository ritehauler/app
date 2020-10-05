import { connect } from "react-redux";
import React, { Component } from "react";
import { View ,TouchableOpacity,Image,Dimensions} from "react-native";
import { Actions } from "react-native-router-flux";
import { Text, BottomButton, FloatLabelTextInput } from "../../components";
import { ApplicationStyles } from "../../theme";
import styles from "./styles";
import { request } from "../../actions/UserActions";
import { API_ENTITY_AUTH_FORGOT_PASS } from "../../config/WebService";
import WithLoader from "../HOC/WithLoader";
import helper from "../../util/helper";
import { USER_ENTITY_TYPE_ID } from "../../constant";
import { request as userRequest } from "../../actions/UserActions";
import ThankyouImg from '../../assets/images/thankyouImg.jpeg';

var {width, height} = Dimensions.get ('window');

class ThankYou extends Component{
  state={
    enable: false
  }

    login() {
        // Keyboard.dismiss();
        // setTimeout(() => {
          Actions.login();
        // }, 200);
      }
    render(){
        return(
            <View style={styles.container}>
                <View>
                    <Text style={styles.signTextTwo}>
                        Thank you!
                    </Text>
                    <Text style={styles.signText}>
                        Thank you for siging up with Rite Hauler Driver! We will be contact with you over the next few days to complete the screening process.

                    </Text>
                </View>
{/* 
                <View style={{width:width,backgroundColor:'black'}}>
                <View style={{flex:1,alignItems:'center',justifyContent:'center'}}>
                <Image 
                     style={{width:width*0.8,height:height*0.4}}
                     source={require('../../assets/images/thankyouImg.jpeg')}/></View>
              </View> */}
      <View style={styles.buttonSignIn}>
      <View style={{width:width,marginBottom: height * 0.1}}>
      <View style={{alignItems:'center'}}>

        <Image 
          style={{width:width * 0.9,height:height*0.4}}
          source={require('../../assets/images/Background.png')}/>
         </View> 
          </View>
          
          <View style={{marginHorizontal:18}}>
          {this.state.enable == false ?
            <View style={styles.disableButton}>
              <Text style={styles.disableTitleButton}> Sign in </Text>
            </View>
            :
            <BottomButton
            title="Sign in"
            style={styles.button}
            onPress={this.login}
          />
          }
          </View>
        </View>
       
 </View>
        );
    }
}
const mapStateToProps = ({ user }) => {
    const componentData = { ...user };
    return { componentData, modal: true };
  };
  
  const actions = {
    userRequest
  };
  
  export default connect(mapStateToProps, actions)(WithLoader(ThankYou));
  