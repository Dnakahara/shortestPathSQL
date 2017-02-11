#include<fstream>
#include<iostream>
#include<vector>
#include<string>
#include<sstream>
#include<queue>
#include<stack>
#include<functional>
#include<chrono>

using namespace std;
using namespace chrono;
using ll = long long;
using P  = pair<ll,int>;

struct edge{
    int to, cost;
    edge(const int& to, const int& cost = 1): to(to), cost(cost){}
    edge(const edge &e): to(e.to), cost(e.cost) {}
};

constexpr int V_NUM = 5000;
constexpr int START_V = 0;
constexpr int GOAL_V = V_NUM - 1;

vector<ll> V(V_NUM, -1);
vector<vector<edge>> Es(V_NUM);
vector<ll> tmpVs;
vector<vector<edge>> tmpEs;
stack<P> st;
queue<P> q;
priority_queue<P, vector<P>, greater<P>> pq;

ll dfs(vector<ll>& tmpVs, vector<vector<edge>>& tmpEs){
    ll ret = 0;
    tmpVs[START_V] = 0;
    st.emplace(P(0, START_V));
    while(!st.empty()){
        P state = st.top(); st.pop();
        ll nowTotal = state.first;
        int nowV = state.second;
        if (tmpVs[nowV] != -1 && tmpVs[nowV] < nowTotal) continue;
        for(int i = 0; i < tmpEs[nowV].size(); ++i){
            edge e = tmpEs[nowV][i];
            if(tmpVs[e.to] == -1 || tmpVs[e.to] > tmpVs[nowV] + e.cost){
                tmpVs[e.to] = tmpVs[nowV] + e.cost;
                st.emplace(P(tmpVs[e.to], e.to));
            }
        }
    }
    ret = tmpVs[GOAL_V];
    return ret;
}

ll bfs(vector<ll>& tmpVs, vector<vector<edge>>& tmpEs){
    ll ret = 0;
    tmpVs[START_V] = 0;
    q.emplace(P(0, START_V));
    while(!q.empty()){
        P state = q.front(); q.pop();
        ll nowTotal = state.first;
        int nowV = state.second;
        if (tmpVs[nowV] != -1 && tmpVs[nowV] < nowTotal) continue;
        for(int i = 0; i < tmpEs[nowV].size(); ++i){
            edge e = tmpEs[nowV][i];
            if(tmpVs[e.to] == -1 || tmpVs[e.to] > tmpVs[nowV] + e.cost){
                tmpVs[e.to] = tmpVs[nowV] + e.cost;
                q.emplace(P(tmpVs[e.to], e.to));
            }
        }
    }
    ret = tmpVs[GOAL_V];
    return ret;
}

ll dijkstra(vector<ll>& tmpVs, vector<vector<edge>>& tmpEs){
    ll ret = 0;
    tmpVs[START_V] = 0;
    pq.emplace(P(0, START_V));
    while (!pq.empty()) {
        P state = pq.top(); pq.pop();
        ll nowTotal = state.first;
        int nowV = state.second;
        if (tmpVs[nowV] != -1 && tmpVs[nowV] < nowTotal) continue;
        for (int i = 0; i < tmpEs[nowV].size(); i++) {
            edge e = tmpEs[nowV][i];
            if (tmpVs[e.to] == -1 || tmpVs[e.to] > tmpVs[nowV] + e.cost) {
                tmpVs[e.to] = tmpVs[nowV] + e.cost;
                pq.emplace(P(tmpVs[e.to], e.to));
            }
        }
    }
    ret = tmpVs[GOAL_V];
    return ret;
}

int main(int argc, char** argv)
{
    ifstream ifs("GraphE.csv");
    if(!ifs){
        cout<<"couldn't open file"<<endl;
        return 1;
    }

    string row;
    while(getline(ifs,row)){
        string token;
        istringstream istrstr(row);

        int e_data[3];int idx = 0;
        while(getline(istrstr, token, ',')){
            stringstream strstr;
            strstr << token;
            strstr >> e_data[idx++];
        }
        Es[e_data[0] - 1].emplace_back(edge(e_data[1] - 1, e_data[2]));
    }

    tmpVs = V;
    tmpEs = Es;
    
    auto duration = std::chrono::high_resolution_clock::now() - time_point;

auto count = std::chrono::duration_cast<std::chrono::microseconds>(duration).count();

    ll ret = 0;
    auto startT = system_clock::now();
    //auto startT = high_resolution_clock::now();
    ret = dfs(tmpVs, tmpEs);
    auto endT = system_clock::now();
    //auto endT = high_resolustion_clock::now();
    auto t = duration_cast<milliseconds>(endT - startT).count();
    //auto t = duration_cast<microseconds>(endT - startT).count();
    cout << "DFS   result:" << ret << " time:" << t << "ms" << endl;
    //cout << "DFS   result:" << ret << " time:" << t << "μs" << endl;
    
    tmpVs = V;
    tmpEs = Es;

    startT = system_clock::now();
    ret = bfs(tmpVs, tmpEs);
    endT = system_clock::now();
    t = duration_cast<milliseconds>(endT - startT).count();
    cout << "BFS   result:" << ret << " time:" << t << "ms" << endl;

    tmpVs = V;
    tmpEs = Es;

    startT = system_clock::now();
    ret = dijkstra(tmpVs, tmpEs);
    endT = system_clock::now();
    t = duration_cast<milliseconds>(endT - startT).count();
    cout << "Dijkstra   result:" << ret << " time:" << t << "ms" << endl;
    
    return 0;
}
