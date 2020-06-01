// @flow
import React, { PureComponent } from "react";
import { View, TextInput, Text } from "react-native";
import { Actions } from "react-native-router-flux";
import { Metrics, ApplicationStyles, Colors } from "../../../theme";
import { BottomButton } from "../../../components";
import Utils from "../../../util";

class DeclineReason extends PureComponent {
  state = {
    reason: ""
  };

  onDonePress = () => {
    if (!this.state.reason.length) {
      Utils.showMessage("Decline reason required");
    } else {
      this.props.cbOnDonePress(this.state.reason);
    }
  };

  render() {
    return (
      <View style={styles.mainContainer}>
        <View style={styles.container}>
          <View>
            <Text
              style={[ApplicationStyles.re16Black, styles.horizontalMargin]}
            >
              Decline Reason
            </Text>
            <View style={styles.inputWrapper}>
              <TextInput
                multiline
                placeholder="Enter decline reason"
                onChangeText={value =>
                  this.setState({
                    reason: value
                  })
                }
                style={[ApplicationStyles.re13Black, styles.input]}
                autoFocus
                underlineColorAndroid="transparent"
              />
            </View>
            <BottomButton title="Done" onPress={this.onDonePress} />
          </View>
        </View>
      </View>
    );
  }
}

const styles = {
  mainContainer: {
    flex: 1,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    alignItems: "center",
    justifyContent: "center"
  },
  container: {
    width: Metrics.screenWidth - Metrics.doubleBaseMargin,
    backgroundColor: Colors.background.primary,
    paddingTop: Metrics.baseMargin,
    marginHorizontal: Metrics.doubleBaseMargin,
    marginBottom: Utils.isPlatformAndroid() ? 0 : Metrics.doubleBaseMargin * 3
  },
  inputWrapper: {
    borderWidth: Metrics.ratio(1),
    borderColor: Colors.background.login,
    borderRadius: Metrics.ratio(2),
    paddingBottom: Metrics.smallMargin,
    marginVertical: Metrics.baseMargin,
    height: Metrics.screenHeight / 4,
    marginHorizontal: Metrics.baseMargin
  },
  input: {
    paddingHorizontal: Metrics.baseMargin
    //paddingTop: Metrics.ratio(-0.1)
  },
  horizontalMargin: {
    marginHorizontal: Metrics.baseMargin
  }
};

export default DeclineReason;
