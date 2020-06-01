// @flow
import React, { Component } from "react";
import { Keyboard, View } from "react-native";
import { Actions } from "react-native-router-flux";
import { PaymentCardTextField } from "tipsi-stripe";

import styles from "./styles";
import { Strings, Colors } from "../../theme";
import { RightViewNavigation } from "../../appComponents";
import Utils from "../../util";

class AddCard extends Component {
  constructor(props) {
    super(props);
    this.saveCard = this.saveCard.bind(this);
  }

  state = {
    valid: false,
    params: {
      number: "",
      expMonth: 0,
      expYear: 0,
      cvc: ""
    }
  };

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    Actions.refresh({
      right: () => <RightViewNavigation text="Save" action={this.saveCard} />
    });
  }

  saveCard() {
    if (this.state.valid) {
      //console.log("params", this.state.params);
      Actions.pop();
    } else {
      Utils.alert(Strings.validCardDetails);
    }
  }

  handleFieldParamsChange = (valid, params) => {
    this.setState({
      valid,
      params
    });
  };

  _renderCard() {
    return (
      <View style={styles.cardContainer}>
        <PaymentCardTextField
          accessible={false}
          style={styles.field}
          onParamsChange={this.handleFieldParamsChange}
          cursorColor={Colors.accent}
          numberPlaceholder={Strings.cardPlaceHolder}
        />
      </View>
    );
  }

  render() {
    return <View style={styles.container}>{this._renderCard()}</View>;
  }
}

export default AddCard;
