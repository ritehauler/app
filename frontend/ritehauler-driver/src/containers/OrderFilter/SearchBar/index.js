import React, { PureComponent } from "react";
import { View, Text, Image } from "react-native";
import Search from "react-native-search-box";
import { Metrics, Colors, ApplicationStyles, Images } from "../../../theme";

class SearchBar extends PureComponent<{}> {
  render() {
    const searchIcon = (
      <Image
        source={Images.search}
        style={{ width: Metrics.icon.small, height: Metrics.icon.small }}
        resizeMode="contain"
      />
    );

    const deleteIcon = (
      <View style={{ backgroundColor: "transparent", width: 0, height: 0 }} />
    );
    return (
      <View style={styles.container}>
        <View style={{ width: Metrics.screenWidth - Metrics.ratio(100) }}>
          <Search
            inputStyle={[
              {
                backgroundColor: Colors.background.primary
              },
              ApplicationStyles.re14Black
            ]}
            onChangeText={text => console.log("search : ", text)}
            placeholder="Search for order ID"
            placeholderTextColor="#bebebe"
            backgroundColor={Colors.background.primary}
            iconSearch={searchIcon}
            iconDelete={deleteIcon}
            returnKeyType="done"
            searchIconCollapsedMargin={Metrics.doubleBaseMargin}
            placeholderCollapsedMargin={Metrics.baseMargin}
            searchIconExpandedMargin={Metrics.baseMargin}
            placeholderExpandedMargin={Metrics.doubleBaseMargin}
            // contentWidth={Metrics.screenWidth - Metrics.ratio(100)}
            // middleWidth={Metrics.screenWidth - Metrics.ratio(150)}
          />
        </View>
        <View
          style={{
            position: "absolute",
            right: 0,
            top: 0,
            bottom: 0,
            alignItems: "center",
            flexDirection: "row",
            justifyContent: "center",
            width: Metrics.ratio(100),
            borderLeftColor: Colors.background.login,
            borderLeftWidth: Metrics.ratio(1),
            backgroundColor: Colors.background.primary
          }}
        >
          <Text
            style={[
              { marginRight: Metrics.smallMargin },
              ApplicationStyles.re14Black
            ]}
          >
            Payment
          </Text>
          <Image source={Images.arrowDown} />
        </View>
      </View>
    );
  }
}

export default SearchBar;

const styles = {
  container: {}
};
